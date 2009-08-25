<?php

abstract class dmSearchPageDocument extends Zend_Search_Lucene_Document
{
  protected static
  $skipWidgets = array(
    'dmWidgetNavigation.breadCrumb',
    'dmWidgetContent.link',
    'dmWidgetContent.media',
    'dmWidgetContent.gallery'
  );
  
	public function __construct(DmPage $page)
	{
		$i18n = $page['Translation'][sfDoctrineRecord::getDefaultCulture()];
		
		$this->store('page_id', $page->get('id'));

    $this->index('body', $this->getPageBodyText($page));

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
  	
    dmContext::getInstance()->setPage($page);
    
    $area = dmDb::query('DmArea a, a.Zones z, z.Widgets w')
      ->select('a.id, z.width, z.css_class, w.module, w.action, w.value, w.css_class')
      ->where('a.type = ? AND a.dm_page_view_id = ?', array('content', $page->get('PageView')->get('id')))
      ->fetchArray();
    
    $helper = dmContext::getInstance()->getPageHelper();
    
    $html = '';
    
    foreach($area[0]['Zones'] as $zone)
    {
      foreach($zone['Widgets'] as $widget)
      {
        if (!in_array($widget['module'].'.'.$widget['action'], self::$skipWidgets))
        {
          $html .= $helper->renderWidgetInner($widget);
        }
      }
    }
    
    unset($area);
    
    $indexableText = dmSearchIndex::cleanText($html);
    
    return $indexableText;
  }

}