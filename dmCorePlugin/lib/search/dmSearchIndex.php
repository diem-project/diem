<?php

require_once('Zend/Search/Lucene.php');

class dmSearchIndex extends dmSearchIndexCommon
{
  protected
  $luceneIndex;
  
  protected function initialize(array $options)
  {
    parent::initialize($options);
    
    if (!$this->getOption('dir'))
    {
      throw new dmSearchIndexException('Can not create an index without dir option');
    }
    
    $this->createLuceneIndex();
  }
  
  public function getFullPath()
  {
    return dmProject::rootify($this->getOption('dir'));
  }

  protected function createLuceneIndex()
  {
    if (file_exists(dmOs::join($this->getFullPath(), 'segments.gen')))
    {
      try
      {
        $this->luceneIndex = Zend_Search_Lucene::open($this->getFullPath());
        $this->luceneIndex->setFormatVersion(Zend_Search_Lucene::FORMAT_2_3);
      }
      catch(Zend_Search_Lucene_Exception $e)
      {
        $this->erase();
      }
    }
    else
    {
      $this->luceneIndex = Zend_Search_Lucene::create($this->getFullPath());
    }
  }

  public function getCulture()
  {
    return $this->getOption('culture');
  }
  
  public function setCulture($culture)
  {
    return $this->setOption('culture', $culture);
  }

  /**
   * Get search results from a search query
   * @param string or Zend_Search_Lucene_Search_Query $query
   * @return array hits
   */
  public function search($query)
  {
    if (!$query instanceof Zend_Search_Lucene_Search_Query)
    {
      $query = $this->getLuceneQuery($this->cleanText($query));
    }

    $hits = array();
    foreach($this->luceneIndex->find($query) as $hit)
    {
      $hits[] = $this->serviceContainer
      ->setParameter('search_hit.score', $hit->score)
      ->setParameter('search_hit.page_id', $hit->page_id)
      ->setParameter('search_hit.page_content', $hit->content)
      ->getService('search_hit');
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

  public function populate()
  {
    $start  = microtime(true);
    $logger = $this->serviceContainer->getService('logger');
    $user   = $this->serviceContainer->getService('user');
    
    $logger->log($this->getName().': Populating index...');

    $this->erase();
    
    $this->serviceContainer->mergeParameter('search_document.options', array(
      'culture' => $this->getCulture()
    ));

    $pager = $this->serviceContainer
    ->setParameter('doctrine_pager.model', 'DmPage')
    ->getService('doctrine_pager')
    ->setMaxPerPage(100)
    ->setQuery($this->getPagesQuery())
    ->setPage(1)
    ->init();

    $nb = 1;
    $nbMax = $pager->getNbResults();
    $pagerPage = 1;
    $pagerPageMax = $pager->getLastPage();
    
    if (!count($nbMax))
    {
      $logger->log($this->getName().': No pages to populate the index');
      return;
    }
    
    $oldCulture = $user->getCulture();
    $user->setCulture($this->getCulture());

    while($pagerPage <= $pagerPageMax)
    {
      foreach ($pager->getResultsWithoutCache() as $page)
      {
        $logger->log($this->getName().' '.$nb.'/'.$nbMax.': /'.$page->get('slug'));

        $document = $this->serviceContainer
        ->setParameter('search_document.source', $page)
        ->getService('search_document');

        try
        {
          $document->populate();
          $this->luceneIndex->addDocument($document);
        }
        catch(dmSearchPageNotIndexableException $e)
        {
          $logger->log('SKIPPED '.$page->get('slug'));
        }

        ++$nb;
      }

      ++$pagerPage;
      $pager->setPage($pagerPage)->init(true);
    }
    
    $user->setCulture($oldCulture);

    $time = microtime(true) - $start;

    $logger->log($this->getName().': Index populated in ' . round($time, 2) . ' seconds.');

    $logger->log($this->getName().': Time per document ' . round($time / $nbMax, 3) . ' seconds.');

    $this->serviceContainer->get('dispatcher')->notify(new sfEvent($this, 'dm.search.populated', array(
      'culture' => $this->getCulture(),
      'name' => $this->getName(),
      'nb_documents' => $nbMax,
      'time' => $time
    )));
    
    $this->fixPermissions();
  }

  public function optimize()
  {
    $start = microtime(true);
    $logger = $this->serviceContainer->getService('logger')->log($this->getName().': Optimizing index...');
    
    $this->luceneIndex->optimize();
    
    $this->fixPermissions();

    $logger = $this->serviceContainer->getService('logger')->log($this->getName().': Index optimized in "' . round(microtime(true) - $start, 2) . '" seconds.');
  }

  protected function erase()
  {
    $this->serviceContainer->getService('filesystem')->deleteDirContent($this->getFullPath());
    
    $this->createLuceneIndex();
  }

  public function getPagesQuery()
  {
    return dmDb::table('DmPage')
    ->createQuery('p')
    ->withI18n($this->getCulture())
    ->where('pTranslation.is_active = ?', true)
    ->andWhere('pTranslation.is_secure = ?', false)
    ->andWhere('p.module != ? OR ( p.action != ? AND p.action != ? AND p.action != ?)', array('main', 'error404', 'search', 'signin'));
  }

  /**
   * @return array of words we do not want to index ( like "the", "a", to"... )
   */
  public function getStopWords()
  {
    return str_word_count(dmString::strtolower(dmConfig::get('search_stop_words')), 1);
  }

  public function describe()
  {
    return array(
      'Documents' => $this->luceneIndex->numDocs(),
      'Size'      => dmOs::humanizeSize($this->getByteSize())
    );
  }

  /**
   * @return Zend_Search_Lucene_Proxy instance
   */
  public function getLuceneIndex()
  {
    return $this->luceneIndex;
  }

  public static function cleanText($text)
  {
    return trim(
    preg_replace('|\s{2,}|', ' ',
    preg_replace('|\W|', ' ',
    dmString::strtolower(
    dmString::transliterate(
    strip_tags(
    str_replace(array("\n", '<'), array(' ', ' <'), $text)
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
    foreach (new DirectoryIterator($this->getFullPath()) as $node)
    {
      $size += $node->getSize();
    }

    return $size;
  }
}