<?php

class dmSearchPageDocument extends Zend_Search_Lucene_Document
{
  protected
  $context,
  $source,
  $options = array(
    'boost_values' => array(
      'body' => 1,
      'slug' => 3,
      'name' => 3,
      'title' => 4,
      'h1' => 4,
      'description' => 3,
      'keywords' => 5
    )
  );
  
  public function __construct(dmContext $context, $source, array $options = array())
  {
    $this->context  = $context;
    $this->source   = $source;
    
    $this->initialize($options);
  }
  
  protected function initialize(array $options)
  {
    $this->options  = sfToolkit::arrayDeepMerge($this->options, $options);
    
    if (!$this->source instanceof DmPage)
    {
      throw new dmException(sprintf('%s require a source instance of DmPage, %s given', get_class($this), get_class($this->source)));
    }
  }
  
  public function populate()
  {
    $i18n = $this->source->getCurrentTranslation();

    $boostValues = $this->getBoostValues($this->source);
    
    $this->store('page_id', $this->source->get('id'));

    if($boostValues['body'])
    {
      $this->index('body', $this->getPageBodyText(), $boostValues['body']);
    }
    
    $this->index('slug', dmString::unSlugify($i18n->get('slug')), $boostValues['slug']);

    $this->index('name', $i18n->get('name'), $boostValues['name']);

    $this->index('title', $i18n->get('title'), $boostValues['title']);

    $this->index('h1', $i18n->get('h1'), $boostValues['h1']);

    $this->index('description', $i18n->get('description'), $boostValues['description']);

    if (sfConfig::get('dm_seo_use_keywords'))
    {
      $this->index('keywords', $i18n->get('keywords'), $boostValues['keywords']);
    }
  }

  protected function getBoostValues()
  {
    return $this->context->getEventDispatcher()->filter(
      new sfEvent($this, 'dm.search.filter_boost_values', array('page' => $this->source)),
      $this->options['boost_values']
    )->getReturnValue();
  }
  
  protected function store($name, $value, $boost = 1)
  {
    $field = Zend_Search_Lucene_Field::UnIndexed($name, $value);
    $field->boost = $boost;
    $this->addField($field);
  }
  
  protected function index($name, $value, $boost = 1)
  {
    $field = Zend_Search_Lucene_Field::UnStored($name, $value);
    $field->boost = $boost;
    $this->addField($field);
  }

  /**
   * @todo retrieve html nodes text ( better than Zend_Search_Lucene_Document_Html )
   */
  protected function getPageBodyText()
  {
    if (sfConfig::get('sf_app') !== 'front')
    {
      throw new dmException('Can only be used in front app ( current : '.sfConfig::get('sf_app').' )');
    }
    
    $page     = $this->source;
    $culture  = $this->options['culture'];
    
    $this->context->setPage($page);
    
    $helper             = $this->context->get('page_helper');
    $widgetTypeManager  = $this->context->get('widget_type_manager');
    
    $areas = dmDb::query('DmPageView pv, pv.Area a')
    ->select('a.id')
    ->where('pv.module = ? AND pv.action = ?', array($page->get('module'), $page->get('action')))
    ->fetchPDO();
    
    $zones = dmDb::query('DmZone z')
    ->leftJoin('z.Widgets w')
    ->innerJoin('w.Translation wTranslation WITH wTranslation.lang = ?', $culture)
    ->select('z.dm_area_id, w.module, w.action, wTranslation.value')
    ->where('z.dm_area_id = ?',$areas[0][0])
    ->fetchArray();
    
    sfConfig::set('dm_search_populating', true);
    
    $html = '';
    
    foreach($zones as $zone)
    {
      foreach($zone['Widgets'] as $widget)
      {
        $widget['value'] = isset($widget['Translation'][$culture]['value']) ? $widget['Translation'][$culture]['value'] : '';
        unset($widget['Translation']);
        
        $widgetType = $widgetTypeManager->getWidgetType($widget['module'], $widget['action']);

        $this->context->getServiceContainer()->addParameters(array(
          'widget_view.class' => $widgetType->getViewClass(),
          'widget_view.type'  => $widgetType,
          'widget_view.data'  => $widget
        ));
        
        $html .= $this->context->get('widget_view')->renderForIndex();
      }
    }
    
    sfConfig::set('dm_search_populating', false);
    
    $indexableText = dmSearchIndex::cleanText($html);
    
    unset($areas, $html, $helper);
    
    return $indexableText;
  }

}