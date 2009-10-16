<?php

class dmSearchPageDocument extends Zend_Search_Lucene_Document
{
  protected
  $context;
  
  public function __construct(dmContext $context)
  {
    $this->context = $context;
  }
  
  public function populate(DmPage $page)
  {
    $i18n = $page['Translation'][sfDoctrineRecord::getDefaultCulture()];
    
    $this->store('page_id', $page->get('id'));

    $this->index('body', $this->getPageBodyText($page), 1);

    $this->index('slug', dmString::unSlugify($i18n->get('slug')), 2);

    $this->index('name', $i18n->get('name'), 2);

    $this->index('title', $i18n->get('title'), 3);

    $this->index('h1', $i18n->get('h1'), 3);

    $this->index('description', $i18n->get('description'), 2);

    $this->index('keywords', $i18n->get('keywords'), 3);
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

  /*
   * @todo retrieve html nodes text ( better than Zend_Search_Lucene_Document_Html )
   */
  protected function getPageBodyText(DmPage $page)
  {
    if (sfConfig::get('sf_app') != 'front')
    {
      throw new dmException('Can only be used in front app ( current : '.sfConfig::get('sf_app').' )');
    }
    
    $this->context->setPage($page);
    
    $helper = $this->context->get('page_helper');
    
    $area = dmDb::query('DmPageView pv, pv.Area a')
    ->select('a.id')
    ->where('pv.module = ? AND pv.action = ?', array($page->get('module'), $page->get('action')))
    ->fetchPDO();
    
    $zones = dmDb::query('DmZone z, z.Widgets w')
      ->select('z.dm_area_id, w.module, w.action, w.value')
      ->where('z.dm_area_id = ?',$area[0][0])
      ->fetchArray();
    
    sfConfig::set('dm_search_populating', true);
    
    $html = '';
    
    foreach($zones as $zone)
    {
      foreach($zone['Widgets'] as $widget)
      {
        $widgetType = $this->context->get('widget_type_manager')->getWidgetType($widget['module'], $widget['action']);

        $this->context->getServiceContainer()->addParameters(array(
          'widget_view.class' => $widgetType->getViewClass(),
          'widget_view.type'  => $widgetType,
          'widget_view.data'  => $widget
        ));
        
        $html .= $this->context->getServiceContainer()->getService('widget_view')->renderForIndex();
      }
    }
    
    sfConfig::set('dm_search_populating', false);
    
    $indexableText = dmSearchIndex::cleanText($html);
    
    unset($area, $html, $helper);
    
    return $indexableText;
  }

}