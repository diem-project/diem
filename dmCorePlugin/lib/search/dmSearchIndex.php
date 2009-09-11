<?php

require_once('Zend/Search/Lucene.php');

class dmSearchIndex extends dmSearchIndexCommon
{
	protected
	$location,
	$index,
	$culture,
	$shortWordLength = 2
	;

	protected static
	$defaultFieldBoosts = array(
	  'id'             => 0,
	  'slug'           => 2,
	  'name'           => 2,
    'title'          => 2,
    'h1'             => 2,
	  'description'    => 1,
	  'content'        => 1
	);

	public function __construct($culture)
	{
		$this->culture = $culture;

		$this->setName('dm_'.$culture);

		$this->setLogger(new dmLoggerBlackhole);

		$this->location = dmOs::join(sfConfig::get('dm_data_dir'), 'index', $this->getName());

		$this->initialize();
	}

	public function getCulture()
	{
		return $this->culture;
	}

	public function search($query)
	{
		$this->open();

		$query = $this->cleanText($query);

		$luceneHits = $this->index->find($this->getLuceneQuery($query));
		$hits = array();
		foreach($luceneHits as $hit)
		{
			$hits[] = new dmSearchHit($hit->score, $hit->page_id);
		}
		unset($luceneHits);

		return $hits;
	}

	protected function getLuceneQuery($query)
	{
		$words = str_word_count($query, 1);
		
		$query = new Zend_Search_Lucene_Search_Query_Boolean();
		
		foreach($words as $word)
		{
			$term = new Zend_Search_Lucene_Index_Term($word);
			$subQuery = new Zend_Search_Lucene_Search_Query_Fuzzy($term, 0.4);
		  $query->addSubquery($subQuery, true);
		}
		
		return $query;
		
		//  return Zend_Search_Lucene_Search_QueryParser::parse($query);
//		$term = new Zend_Search_Lucene_Index_Term($query);
//		return new Zend_Search_Lucene_Search_Query_Fuzzy($term, 0.4);
	}

	public function populate()
	{
		$start = microtime(true);
		$this->getLogger()->log('Populating index...', $this->getName());

		$this->erase();
		$this->getLogger()->log('Index erased.', $this->getName());

		$user = dm::getUser();
		$oldCulture = $user->getCulture();
		$culture = $this->getCulture();
		$user->setCulture($this->getCulture());

		$pages = $this->getPagesQuery()->fetchRecords();

		foreach ($pages as $page)
		{
			$this->getLogger()->log($page->get('slug'), 'Indexing page');
			$this->index->addDocument(new dmSearchPageDocument($page));
		}

		$user->setCulture($oldCulture);

		$time = microtime(true) - $start;

		$this->getLogger()->log('Index populated in "' . round($time, 2) . '" seconds.', $this->getName());

		$this->getLogger()->log('Time per document "' . round($time / count($pages), 3) . '" seconds.', $this->getName());

		unset($pages);
	}

	public function optimize()
	{
		$this->open();
		$this->index->optimize();
	}

	protected function open()
	{
		if (null === $this->index)
		{
			if (file_exists(dmOs::join($this->location, 'segments.gen')))
			{
				try
				{
					$this->index = Zend_Search_Lucene::open($this->location);
				}
				catch(Zend_Search_Lucene_Exception $e)
				{
					$this->erase();
				}
			}
			else
			{
				$this->index = Zend_Search_Lucene::create($this->location);
			}
		}
	}

	protected function close()
	{
		unset($this->index);
		$this->index = null;
	}

	protected function erase()
	{
		$this->filesystem->deleteDirContent($this->location);
		$this->index = Zend_Search_Lucene::create($this->location);
	}

	protected function configure()
	{
	}

	protected function createService()
	{
		$service = new xfService($this->getServiceIdentifier());

		$service->addBuilder(new dmSearchDoctrineBuilder($this->getServiceFields()));

		$service->addRetort(new dmSearchRetortPage);

		return $service;
	}

	protected function getPagesQuery()
	{
		return dmDb::table('DmPage')
		->createQuery('p')
		->withI18n($this->getCulture())
		->where('translation.is_active = ? AND p.is_secure = ? AND ( p.module != ? OR ( p.action != ? AND p.action != ?))', array(true, false, 'main', 'error404', 'search'));
	}

	protected function getServiceFields()
	{
		$fieldBoosts = sfConfig::get('dm_search_fields', self::$defaultFieldBoosts);

		$fields = array();
		foreach($fieldBoosts as $fieldName => $boost)
		{
			switch($fieldName)
			{
				case 'id':       $type = xfField::UNINDEXED; break;
				default:         $type = xfField::UNSTORED;
			}
			$field = new xfField($fieldName, $type);
			$field->setBoost($boost);
			$field->registerCallback(array($this, 'cleanText'));
			if ($fieldName === 'slug')
			{
				$field->registerCallback(array('dmString', 'unSlugify'));
			}
			$fields[] = $field;
		}

		return $fields;
	}

	protected function createEngine()
	{
		$indexStorageLocation = dmOs::join(sfConfig::get('sf_data_dir'), 'index', $this->getName());

		$engine = new xfLuceneEngine($indexStorageLocation);

		$engine->setAnalyzer($this->createAnalyzer());

		return $engine;
	}

	protected function createAnalyzer()
	{
		$analyzer = new xfLuceneAnalyzer(xfLuceneAnalyzer::TEXT);
		$analyzer->addStopWords($this->getStopWords());
		$analyzer->setShortWordLength($this->shortWordLength);
		$analyzer->setCaseInsensitive();

		return $analyzer;
	}

	/*
	 * @return array of words we do not want to index ( like "the", "a", to"... )
	 */
	public function getStopWords()
	{
		$stopWords = dmConfig::get('search_stop_words');
		$stopWords = str_word_count(strtolower($stopWords), 1);
		return $stopWords;
	}


	public function describe()
	{
		$this->open();

		return array(
	    'Documents' => $this->index->numDocs(),
	    'Size'      => dmOs::humanizeSize($this->getByteSize())
		);
	}

	public static function refresh($input)
	{
		$this->remove($input);
		$this->insert($input);
	}

	public static function cleanText($text)
	{
		return trim(
		preg_replace('|\s{2,}|', ' ',
		preg_replace('|\W|', ' ',
		strtolower(
		dmString::removeAccents(
		strip_tags(
		str_replace('<', ' <', $text)
		)
		)
		)
		)
		)
		);
	}

	/**
	 * Gets the byte size of the index.
	 *
	 * @returns int The size in bytes
	 */
	public function getByteSize()
	{
		$size = 0;
		foreach (new DirectoryIterator($this->location) as $node)
		{
			if (!in_array($node->getFilename(), array('CVS', '.svn', '_svn')))
			{
				$size += $node->getSize();
			}
		}

		return $size;
	}

}