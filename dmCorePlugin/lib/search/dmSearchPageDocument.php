<?php

class dmSearchPageDocument extends Zend_Search_Lucene_Document
{
  protected
  $context,
  $source,
  $options;
  
  public function __construct(dmContext $context, $source, array $options = array())
  {
    $this->context  = $context;
    $this->source   = $source;
    
    $this->initialize($options);
  }
  
  protected function initialize(array $options)
  {
    $this->options  = $options;
    
    if (!$this->source instanceof DmPage)
    {
      throw new dmException(sprintf('%s require a source instance of DmPage, %s given', get_class($this), get_class($this->source)));
    }
  }
  
  public function populate()
  {
    $page = $this->source;
    $i18n = $page->getCurrentTranslation();
    
    $this->store('page_id', $page->get('id'));

    $this->index('body', $this->getPageBodyText(), 1);

    $this->index('slug', dmString::unSlugify($i18n->get('slug')), 3);

    $this->index('name', $i18n->get('name'), 3);

    $this->index('title', $i18n->get('title'), 4);

    $this->index('h1', $i18n->get('h1'), 4);

    $this->index('description', $i18n->get('description'), 3);

    if (sfConfig::get('dm_seo_use_keywords'))
    {
      $this->index('keywords', $i18n->get('keywords'), 5);
    }
    
    return $this;
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
    
    $helper = $this->context->get('page_helper');
    
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
        
        $widgetType = $this->context->get('widget_type_manager')->getWidgetType($widget['module'], $widget['action']);

        $this->context->getServiceContainer()->addParameters(array(
          'widget_view.class' => $widgetType->getViewClass(),
          'widget_view.type'  => $widgetType,
          'widget_view.data'  => $widget
        ));
        
        $text .= $this->context->get('widget_view')->renderForIndex();
      }
    }
    
    sfConfig::set('dm_search_populating', false);
    
    $indexableText = dmSearchIndex::cleanText($text);
    
    unset($areas, $html, $helper);
    
    return $indexableText;
  }

}