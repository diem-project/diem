<?php

class dmPageIndexableContentTask extends dmContextTask
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
      throw new dmException('deprecated');
      
      $this->withDatabase();

      $page = dmDb::table('DmPage')->findOneByIdWithI18n($arguments['id'], $arguments['culture']);
       
      if (!$page instanceof DmPage)
      {
        throw new dmException('No page with id = '.$arguments['id']);
      }
       
      $this->getContext()->setPage($page);

      $area = dmDb::query('DmArea a, a.Zones z, z.Widgets w')
      ->select('a.id, z.id, w.module, w.action, w.value')
      ->where('a.type = ? AND a.dm_page_view_id = ?', array('content', $page->get('PageView')->get('id')))
      ->orderBy('z.position asc, w.position asc')
      ->fetchArray();
       
      $widgetTypeManager = $dmContext->get('widget_type_manager');

      $html = '';
       
      foreach($area[0]['Zones'] as $zone)
      {
        foreach($zone['Widgets'] as $widget)
        {
          if (!in_array($widget['module'].'.'.$widget['action'], self::$skipWidgets))
          {
            $widget['css_class'] = null;
            
            $widgetViewClass = $widgetTypeManager->getWidgetType($widget['module'], $widget['action'])->getViewClass();

            $widgetView = new $widgetViewClass($widget);
            
            try
            {
              $html .= $widgetView->toIndexableString();
            }
            catch(Exception $e)
            {
              $this->log($e->getMessage());
            }
          }
        }
      }

      $indexableText = dmSearchIndex::cleanText($html);

      die($indexableText);
    }
}