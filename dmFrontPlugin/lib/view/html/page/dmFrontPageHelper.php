<?php

class dmFrontPageHelper
{
	protected
    $dispatcher,
	  $dmContext,
	  $areas;

  public function __construct(sfEventDispatcher $dispatcher, dmContext $dmContext)
  {
    $this->dispatcher = $dispatcher;
    $this->dmContext  = $dmContext;
    
    $this->initialize();
  }
  
  public function initialize()
  {
    $this->areas = null;
  }
  
  public function getPage()
  {
    return $this->dmContext->getPage();
  }
  
  public function getAreas()
  {
    if (is_null($this->areas))
    {
      $this->areas = dmDb::query('DmArea a INDEXBY a.type, a.Zones z, z.Widgets w')
      ->select('a.type, z.width, z.css_class, w.module, w.action, w.value, w.css_class')
      ->where('a.dm_layout_id = ? OR a.dm_page_view_id = ?', array($this->getPage()->getPageView()->get('Layout')->get('id'), $this->getPage()->getPageView()->get('id')))
      ->orderBy('z.position asc, w.position asc')
      ->fetchArray();
    }
    
    return $this->areas;
  }
  
  public function getArea($type)
  {
  	$this->getAreas();

  	if (!isset($this->areas[$type]))
  	{
  		throw new dmException(sprintf('Page %s with layout %s has no area for type %s', $this->getPage(), $this->getPage()->Layout, $type));
  	  return null;
  	}

  	return $this->areas[$type];
  }

  public function renderAccessLinks()
  {
	  if (!sfConfig::get('dm_accessibility_access_links', true))
	  {
	  	return '';
	  }

	  $html = '<div class="dm_access_links">';

	  $html .= sprintf(
	    '<a href="#content">%s</a>',
	    dm::getI18n()->__('Go to content')
	  );

	  $html .= '</div>';

	  return $html;
  }

  public function renderArea($type)
  {
    $tagName = $this->getAreaTypeTagName($type);

    $area = $this->getArea($type);

    $html = '';

    /*
     * Add a content id for accessibility purpose ( access links )
     */
    if ($type === 'content')
    {
    	$html .= '<div id="dm_content">';
    }

    $html .= sprintf(
      '<%s class="dm_area %s" id="dm_area_%d">',
      $tagName,
      $type === 'content' ? 'dm_content' : 'dm_layout_'.$type,
      $area['id']
    );

    $html .= '<div class="dm_zones clearfix">';

    foreach($area['Zones'] as $zone)
    {
    	$html .= $this->renderZone($zone);
    }

    $html .= '</div>';

    $html .= sprintf('</%s>', $tagName);

    /*
     * Add a content id for accessibility purpose ( access links )
     */
    if ($type === 'content')
    {
      $html .= '</div>';
    }

    return $html;
  }
  
  protected function getAreaTypeTagName($areaType)
  {
    if (sfConfig::get('dm_html_doctype_version', 5) == 5)
    {
      $tagName = dmArray::get(array(
        'top'     => 'header',
        'left'    => 'aside',
        'content' => 'section',
        'right'   => 'aside',
        'bottom'  => 'footer'
      ), $areaType, 'div');
    }
    else
    {
      $tagName = 'div';
    }
    
    return $tagName;
  }

  public function renderZone(array $zone)
  {
    $cssClasses = array('dm_zone', $zone['css_class']);

    $style = (!$zone['width'] || $zone['width'] === '100%') ? '' : " style='width: ".$zone['width'].";'";
    
    $html = sprintf(
      "<div class='%s'%s>",
      dmArray::toHtmlCssClasses($cssClasses),
      $style
    );

    $html .= '<div class="dm_widgets">';

    foreach($zone['Widgets'] as $widget)
    {
      $html .= $this->renderWidget($widget);
    }

    $html .= '</div>';

    $html .= '</div>';

    return $html;
  }

  public function renderWidget(array $widget)
  {
  	list($widgetWrapClass, $widgetInnerClass) = $this->getWidgetContainerClasses($widget);

    /*
     * Open widget wrap with wrapped user's classes
     */
    $html = sprintf('<div class="%s">', $widgetWrapClass);

    /*
     * Open widget inner with user's classes
     */
    $html .= sprintf('<div class="%s">', $widgetInnerClass);

    /*
     * get widget inner content
     */
    $html .= $this->renderWidgetInner($widget);

    /*
     * Close widget inner
     */
    $html .= '</div>';

    /*
     * Close widget wrap
     */
    $html .= '</div>';

    return $html;
  }

  public function renderWidgetInner(array $widget, dmWidgetType $widgetType = null)
  {
//    ob_start();
    
    try
    {
	    if (is_null($widgetType))
	    {
	      $widgetType = $dmContext->getWidgetTypeManager()->getWidgetType($widget['module'], $widget['action']);
	    }
	
	    $widgetViewClass = $widgetType->getViewClass();
	
	    $widgetView = new $widgetViewClass($widget, $this->dmContext);
	    
      $html = $widgetView->render();
      
//      ob_clean();
    }
    catch(Exception $e)
    {
//      ob_clean();
      if (sfConfig::get('dm_debug'))
      {
        throw $e;
      }
      elseif (sfConfig::get('sf_debug'))
      {
        $html = dmFrontLinkTag::build(dm::getRequest()->getUri())
        ->param('dm_debug', 1)
        ->text(sprintf('[%s/%s] : %s', $widget['module'], $widget['action'], $e->getMessage()))
        ->title('Click to see the exception details')
        ->set('.dm_exception.s16.s16_error');
      }
      else
      {
      	$html = '';
      }
    }

    return $html;
  }

  public function getWidgetContainerClasses(array $widget)
  {
    if(!empty($widget['css_class']))
    {
    	$widgetWrappedClasses = explode(' ', $widget['css_class']);
	    foreach($widgetWrappedClasses as $index => $class)
	    {
	      $widgetWrappedClasses[$index] = $class.'_wrap';
	    }
	    
      $widgetWrapClass  = dmArray::toHtmlCssClasses(array('dm_widget', $widget['action'], implode(' ', $widgetWrappedClasses)));
      $widgetInnerClass = dmArray::toHtmlCssClasses(array('dm_widget_inner', $widget['css_class']));
    }
    else
    {
      $widgetWrapClass  = 'dm_widget '.$widget['action'];
      $widgetInnerClass = 'dm_widget_inner';
    }
    
    return array($widgetWrapClass, $widgetInnerClass);
  }
}