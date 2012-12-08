<?php

class dmSearchPageDocument extends Zend_Search_Lucene_Document
{
  protected static
  $pageViewQueryCache,
  $zonesQueryCache = array();
  
  protected
  $context,
  $page,
  $pageContentCache,
  $options = array(
    'boost_values' => array(
      'body'        => 1,
      'slug'        => 3,
      'name'        => 3,
      'title'       => 4,
      'h1'          => 4,
      'description' => 3,
      'keywords'    => 5
    )
  );
  
  public function __construct(dmContext $context, DmPage $page, array $options = array())
  {
    $this->context  = $context;
    $this->page     = $page;
    
    $this->initialize($options);
  }
  
  protected function initialize(array $options)
  {
    $this->options  = sfToolkit::arrayDeepMerge($this->options, $options);
    
    if (!$this->page instanceof DmPage)
    {
      throw new dmException(sprintf('%s require a source instance of DmPage, %s given', get_class($this), get_class($this->page)));
    }
  }

  /**
   * Fill the field values with the page
   */
  public function populate()
  {
    $boostValues = $this->getBoostValues();

    // store the page id without indexing it
    $this->store('page_id', $this->page->get('id'));

    // index the page slug
    $this->index('slug', dmString::unSlugify($this->page->get('slug')), $boostValues['slug']);

    // index the page name
    $this->index('name', $this->page->get('name'), $boostValues['name']);

    // index the page title
    $this->index('title', $this->page->get('title'), $boostValues['title']);

    // index the page h1
    $this->index('h1', $this->page->get('h1'), $boostValues['h1']);

    // index the page description
    $this->index('description', $this->page->get('description'), $boostValues['description']);

    // index keywords only if the project uses them
    if (sfConfig::get('dm_seo_use_keywords'))
    {
      $this->index('keywords', $this->page->get('keywords'), $boostValues['keywords']);
    }

    // process the page body only if its boost value is not null
    if($boostValues['body'])
    {
      $this->index('content_index', $this->getPageContentForIndex(), $boostValues['body']);
    }

    // store page content to display it on search results
    $this->store('content', $this->getPageContentForStore());
  }

  /**
   * Get the boost values for each field
   *
   * @return array the boost values for each field
   */
  protected function getBoostValues()
  {
    return $this->context->getEventDispatcher()->filter(
      new sfEvent($this, 'dm.search.filter_boost_values', array('page' => $this->page)),
      $this->options['boost_values']
    )->getReturnValue();
  }

  /**
   * Store a field without indexing it
   *
   * @param string  $name   the field name
   * @param mixed   $value  the field value
   *
   * @return dmSearchDocument the search_document instance
   */
  protected function store($name, $value)
  {
    $field = Zend_Search_Lucene_Field::unIndexed($name, $value);
    $this->addField($field);
  }

  /**
   * Index a field
   *
   * @param string  $name   the field name
   * @param mixed   $value  the field value
   * @param float   $boost  the boost value
   *
   * @return dmSearchDocument the search_document instance
   */
  protected function index($name, $value, $boost = 1.0)
  {
    $field = Zend_Search_Lucene_Field::unStored($name, $value);
    $field->boost = $boost;
    $this->addField($field);
  }

  /**
   * Get a page indexable content
   *
   * @return string the page text content
   */
  protected function getPageContentForIndex()
  {
    return dmSearchIndex::cleanText($this->getPageContent());
  }

  /**
   * Get a page storable content
   *
   * @return string the page text content
   */
  protected function getPageContentForStore()
  {
    $content = $this->getPageContent();
    
    $markdown = $this->context->getServiceContainer()->get('markdown');                
    
    return $markdown::brutalToText(
      trim(
        preg_replace('|\s{2,}|', ' ',
          strip_tags(
            str_replace(array("\n", '<'), array(' ', ' <'), $content)
          )
        )
      )
    );
  }

  /**
   * Get a page content
   *
   * @return string the page text content
   */
  protected function getPageContent()
  {
    if(null !== $this->pageContentCache)
    {
      return $this->pageContentCache;
    }
    
    if (sfConfig::get('sf_app') !== 'front')
    {
      throw new dmException('Can only be used in front app ( current : '.sfConfig::get('sf_app').' )');
    }
    
    $culture  = $this->options['culture'];
    
    $this->context->setPage($this->page);
    
    $serviceContainer   = $this->context->getServiceContainer();
    $helper             = $serviceContainer->get('page_helper');
    $widgetTypeManager  = $serviceContainer->get('widget_type_manager');
    
    $pageView = self::getPageViewQuery()->fetchArray(array($this->page->get('module'), $this->page->get('action')));

    $areaIds = array();
    foreach($pageView[0]['Areas'] as $area)
    {
      $areaIds[] = $area['id'];
    }
    $zonesQuery = clone self::getZonesQuery($culture);
    $zones = $zonesQuery
    ->whereIn('z.dm_area_id', $areaIds)
    ->fetchArray();
    
    sfConfig::set('dm_search_populating', true);

    $this->pageContentCache = '';
    
    foreach($zones as $zone)
    {
      foreach($zone['Widgets'] as $widget)
      {
        try
        {
          $widget['value'] = isset($widget['Translation'][$culture]['value']) ? $widget['Translation'][$culture]['value'] : '';
          unset($widget['Translation']);

          $widgetType = $widgetTypeManager->getWidgetType($widget['module'], $widget['action']);

          try
          {
            $this->pageContentCache .= ' ' . $serviceContainer
            ->addParameters(array(
              'widget_view.class' => $widgetType->getViewClass(),
              'widget_view.type'  => $widgetType,
              'widget_view.data'  => $widget
            ))
            ->getService('widget_view')
            ->renderForIndex();
          }
          catch(dmFormNotFoundException $e)
          {
            // a form is required but not available, skip this widget
          }
        }
        catch(Exception $e)
        {
          // pass on errors
        }
      }
    }

    sfConfig::set('dm_search_populating', false);
    
    unset($areas, $zones, $html, $helper);
    
    return $this->pageContentCache;
  }

  protected static function getPageViewQuery()
  {
    if(null !== self::$pageViewQueryCache)
    {
      return self::$pageViewQueryCache;
    }

    return self::$pageViewQueryCache = dmDb::query('DmPageView pv, pv.Areas a')
    ->select('pv.id, a.id')
    ->where('pv.module = ?')
    ->andWhere('pv.action = ?');
  }

  protected static function getZonesQuery($culture)
  {
    if(isset(self::$zonesQueryCache[$culture]))
    {
      return self::$zonesQueryCache[$culture];
    }

    return self::$zonesQueryCache[$culture] = dmDb::query('DmZone z')
    ->leftJoin('z.Widgets w')
    ->innerJoin('w.Translation wt')
    ->where('wt.lang = ?', $culture)
    ->select('z.dm_area_id, w.module, w.action, wt.value');
  }

}