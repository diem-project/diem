<?php

class dmPageIndexableContentTask extends dmBaseTask
{

	protected static
	$skipWidgets = array(
	  'dmWidgetNavigation.breadCrumb',
	  'dmWidgetContent.link',
    'dmWidgetContent.media',
    'dmWidgetContent.gallery'
    );

    /**
     * @see sfTask
     */
    protected function configure()
    {
    	$this->namespace = 'dmFront';
    	$this->name = 'page-indexable-content';
    	$this->briefDescription = 'Return page html content for search engine indexation';

    	$this->detailedDescription = $this->briefDescription;

    	$this->addOptions(array(
    	new sfCommandOption('application', null, sfCommandOption::PARAMETER_OPTIONAL, 'The application name', 'front'),
    	new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod')
    	));

    	$this->addArguments(array(
    	new sfCommandArgument('id', sfCommandArgument::REQUIRED, 'The page id'),
    	new sfCommandArgument('culture', sfCommandArgument::REQUIRED, 'The page culture')
    	));
    }

    /**
     * @see sfTask
     */
    protected function execute($arguments = array(), $options = array())
    {
    	if (!sfContext::hasInstance())
    	{
        sfContext::createInstance($this->configuration);
    	}
    	$databaseManager = new sfDatabaseManager($this->configuration);

    	$page = dmDb::table('DmPage')->findOneByIdWithI18n($arguments['id'], $arguments['culture']);
    	 
    	if (!$page instanceof DmPage)
    	{
    		throw new dmException('No page with id = '.$arguments['id']);
    	}
    	 
    	$dmContext = dmContext::getInstance();
    	 
    	$dmContext->setPage($page);

    	$area = dmDb::query('DmArea a, a.Zones z, z.Widgets w')
    	->select('a.id, z.width, z.css_class, w.module, w.action, w.value, w.css_class')
    	->where('a.type = ? AND a.dm_page_view_id = ?', array('content', $page->PageView->id))
    	->orderBy('z.position asc, w.position asc')
    	->fetchArray();
    	 
    	$html = '';
    	 
    	foreach($area[0]['Zones'] as $zone)
    	{
	      foreach($zone['Widgets'] as $widget)
	      {
	      	if (!in_array($widget['module'].'.'.$widget['action'], self::$skipWidgets))
	      	{
	      		$widgetViewClass = dmWidgetTypeManager::getWidgetType($widget['module'], $widget['action'])->getViewClass();
	
	      		$widgetView = new $widgetViewClass($widget);
	
	      		$html .= $widgetView->toIndexableString();
	      	}
	      }
    	}

    	$indexableText = dmSearchIndex::cleanText($html);

    	die($indexableText);
    }
}