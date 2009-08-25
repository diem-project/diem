<?php

class dmInterfaceComponents extends dmFrontBaseComponents
{

  public function executeToolBar()
  {
    $this->cultures = array();
    $languages = sfCultureInfo::getInstance(dm::getUser()->getCulture())->getLanguages();
    foreach(dm::getI18n()->getCultures() as $key)
    {
      $this->cultures[$key] = dmArray::get($languages, $key, $key);
    }

    $this->themes = dmTheme::getList();

    if ($this->getUser()->can('widget_add'))
    {
      $this->addMenu = new dmHtmlMenu($this->addMenu());
    }
  }

  protected function addMenu()
  {
  	$menu = array();
  	
  	if(dm::getUser()->can('zone_add'))
  	{
	  	$menu[] =array(
  	    'name' => 'Zone',
  	    'menu' => array(
  	      array(
  	        'name' => 'Zone',
            'class' => 'zone_add move'
          )
        )
	  	);
  	}

  	foreach(dmWidgetTypeManager::getWidgetTypes() as $space => $widgetTypes)
  	{
  		if (empty($widgetTypes))
  		{
  			continue;
  		}
  		
  		$spaceMenu = array();

  		foreach($widgetTypes as $key => $widgetType)
  		{
  			$spaceMenu[$key] = array(
  			  'name' => dm::getI18n()->__($widgetType->getName()),
  			  'class' => 'widget_add move',
  			  'id' => sprintf('dmwa_%s_%s', $widgetType->getModule(), $widgetType->getAction())
  			);
  		}
  		
  		$spaceName = $space == 'main'
  		? dm::getI18n()->__(dmContext::getInstance()->getSite()->getName())
  		: dm::getI18n()->__(dmString::humanize(str_replace('dmWidget', '', $space)));

  		$menu[$space] = array(
  		  'name' => $spaceName,
  		  'menu' => $spaceMenu
  		);
  	}

  	return array(
  	  array(
	      'name' => dm::getI18n()->__('Add'),
  	    'class' => 'strong',
	      'menu' => $menu
  	  )
    );
  }

}