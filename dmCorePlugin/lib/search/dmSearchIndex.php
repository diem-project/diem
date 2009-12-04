<?php

require_once('Zend/Search/Lucene.php');

class dmSearchIndex extends dmSearchIndexCommon
{
  protected
  $filesystem,
  $location,
  $index,
  $culture,
  $shortWordLength = 2;

  public function __construct(sfEventDispatcher $dispatcher, dmFilesystem $filesystem, sfLogger $logger)
  {
    $this->dispatcher = $dispatcher;
    $this->filesystem = $filesystem;
    $this->logger     = $logger;
  }
  
  public function setLogger(sfLogger $logger)
  {
    $this->logger = $logger;
  }

  public function setCulture($culture)
  {
    $this->culture = $culture;
    
    $this->name = 'dm_'.$culture;

    $this->location = dmOs::join(sfConfig::get('dm_data_dir'), 'index', $this->name);

    $this->initialize();
  }
  
  public function getCulture()
  {
    return $this->culture;
  }

  public function search($query)
  {
    $this->open();

    if (!$query instanceof Zend_Search_Lucene_Search_Query)
    {
      $query = $this->getLuceneQuery($this->cleanText($query));
    }

    $luceneHits = $this->index->find($query);
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
//    $term = new Zend_Search_Lucene_Index_Term($query);
//    return new Zend_Search_Lucene_Search_Query_Fuzzy($term, 0.4);
  }

  public function populate(dmContext $context)
  {
    $start = microtime(true);
    $this->logger->log($this->getName().' : Populating index...');

    $this->erase();
    $this->logger->log($this->getName().' : Index erased.');

    $pages = $this->getPagesQuery()->fetchRecords();
    
    if (!count($pages))
    {
      $this->logger->log($this->getName().' : No pages to populate the index');
      return;
    }

    $nb = 0;
    $nbMax = count($pages);
    foreach ($pages as $page)
    {
      ++$nb;
      $this->logger->log($this->getName().' '.$nb.'/'.$nbMax.' : '.$page->get('slug'));
      $document = $context->get('search_page');
      $document->populate($page);
      $this->index->addDocument($document);
      unset($document);
    }

    $time = microtime(true) - $start;

    $this->logger->log($this->getName().' : Index populated in "' . round($time, 2) . '" seconds.');

    $this->logger->log($this->getName().' : Time per document "' . round($time / count($pages), 3) . '" seconds.');

    $this->dispatcher->notify(new sfEvent($this, 'dm.search.populated', array(
      'name' => $this->getName(),
      'nb_documents' => count($pages),
      'time' => $time
    )));
    
    unset($pages);
    
    $this->fixPermissions();
  }

  public function optimize()
  {
    $start = microtime(true);
    $this->logger->log($this->getName().' : Optimizing index...');
    
    $this->open();
    $this->index->optimize();
    
    $this->fixPermissions();

    $this->logger->log($this->getName().' : Index optimized in "' . round(microtime(true) - $start, 2) . '" seconds.');
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
    if (!$this->culture)
    {
      throw new dmException('culture is required');
    }
  }

  protected function getPagesQuery()
  {
    return dmDb::table('DmPage')
    ->createQuery('p')
    ->withI18n($this->getCulture())
    ->where('pTranslation.is_active = ?', true)
    ->andWhere('p.is_secure = ?', false)
    ->andWhere('p.module != ? OR ( p.action != ? AND p.action != ? AND p.action != ?)', array('main', 'error404', 'search', 'login'));
  }

  /*
   * @return array of words we do not want to index ( like "the", "a", to"... )
   */
  public function getStopWords()
  {
    return str_word_count(strtolower(dmConfig::get('search_stop_words')), 1);
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

  public function fixPermissions()
  {
    foreach (sfFinder::type('all')->in($this->location) as $item)
    {
      $this->filesystem->chmod($item, 0777);
    }
  }

}