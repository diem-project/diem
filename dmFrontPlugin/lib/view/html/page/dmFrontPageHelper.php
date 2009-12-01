<?php

class dmFrontPageHelper
{
  protected
    $dispatcher,
    $widgetTypeManager,
    $serviceContainer,
    $i18n,
    $helper,
    $page,
    $areas,
    $isHtml5;

  protected static
  $innerCssClassWidgets = array('dmWidgetContent.title', 'dmWidgetContent.media', 'dmWidgetContent.link');
    
  public function __construct(sfEventDispatcher $dispatcher, dmWidgetTypeManager $widgetTypeManager, sfServiceContainer $serviceContainer, sfI18n $i18n, dmHelper $helper)
  {
    $this->dispatcher        = $dispatcher;
    $this->widgetTypeManager = $widgetTypeManager;
    $this->serviceContainer  = $serviceContainer;
    $this->i18n              = $i18n;
    $this->helper            = $helper;
    
    $this->initialize();
  }
  
  public function initialize()
  {
    $this->isHtml5 = 5 == $this->getDocTypeOption('version', 5);
  }
  
  public function connect()
  {
    $this->dispatcher->connect('dm.context.change_page', array($this, 'listenToChangePageEvent'));
  }
  
  /**
   * Listens to the user.change_culture event.
   *
   * @param sfEvent An sfEvent instance
   */
  public function listenToChangePageEvent(sfEvent $event)
  {
    $this->setPage($event['page']);
  }
  
  public function setPage(DmPage $page)
  {
    $this->page = $page;
    
    $this->areas = null;
  }
  
  public function getAreas()
  {
    if (null === $this->areas)
    {
      if (null === $this->page)
      {
        throw new dmException('Can not fetch page area because no page have been set');
      }
      
      $this->areas = dmDb::query('DmArea a INDEXBY a.type, a.Zones z, z.Widgets w')
      ->select('a.type, z.width, z.css_class, w.module, w.action, w.value, w.css_class')
      ->where('a.dm_layout_id = ? OR a.dm_page_view_id = ?', array($this->page->getPageView()->getLayout()->get('id'), $this->page->getPageView()->get('id')))
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
      throw new dmException(sprintf('Page %s with layout %s has no area for type %s', $this->page, $this->page->Layout, $type));
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

    $html .= '<a href="#content">'.$this->i18n->__('Go to content').'</a>';

    $html .= '</div>';

    return $html;
  }

  public function renderArea($type, $options = array())
  {
    $options = dmString::toArray($options);
    
    $tagName = $this->getAreaTypeTagName($type);

    $area = $this->getArea($type);
    
    $options['class'] = array_merge(dmArray::get($options, 'class', array()), array(
      'dm_area',
      'content' === $type ? 'dm_content' : 'dm_layout_'.$type
    ));
    
    $options['id'] = dmArray::get($options, 'id', 'dm_area_'.$area['id']);

    $html = '';

    /*
     * Add a content id for accessibility purpose ( access links )
     */
    if ('content' === $type)
    {
      $html .= '<div id="dm_content">';
    }
    
    $html .= $this->helper->£o($tagName, $options);

    $html .= '<div class="dm_zones">';

    if (!empty($area['Zones']))
    {
      foreach($area['Zones'] as $zone)
      {
        $html .= $this->renderZone($zone);
      }
    }

    $html .= '</div>';

    $html .= sprintf('</%s>', $tagName);

    /*
     * Add a content id for accessibility purpose ( access links )
     */
    if ('content' === $type)
    {
      $html .= '</div>';
    }

    return $html;
  }
  
  protected function getAreaTypeTagName($areaType)
  {
    if ($this->isHtml5())
    {
      switch($areaType)
      {
        case 'top':     $tagName = 'header'; break;
        case 'left':    $tagName = 'aside'; break;
        case 'content': $tagName = 'section'; break;
        case 'right':   $tagName = 'aside'; break;
        case 'bottom':  $tagName = 'footer'; break;
        default:        $tagName = 'div';
      }
    }
    else
    {
      $tagName = 'div';
    }
    
    return $tagName;
  }

  public function renderZone(array $zone)
  {
    $style = (!$zone['width'] || $zone['width'] === '100%') ? '' : ' style="width: '.$zone['width'].';"';
    
    $html = '<div class="'.dmArray::toHtmlCssClasses(array('dm_zone', $zone['css_class'])).'"'.$style.'>';

    $html .= '<div class="dm_widgets">';
    
    if(!empty($zone['Widgets']))
    {
      foreach($zone['Widgets'] as $widget)
      {
        $html .= $this->renderWidget($widget);
      }
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
    $html = '<div class="'.$widgetWrapClass.'">';

    /*
     * Open widget inner with user's classes
     */
    $html .= '<div class="'.$widgetInnerClass.'">';

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

  public function renderWidgetInner(array $widget)
  {
    try
    {
      $this->serviceContainer->setParameter('widget_renderer.widget', $widget);
      
      $renderer = $this->serviceContainer->getService('widget_renderer');
      
      $html = $renderer->getHtml();
    
      foreach($renderer->getJavascripts() as $javascript)
      {
        $this->serviceContainer->getService('response')->addJavascript($javascript);
      }
      
      foreach($renderer->getStylesheets() as $stylesheet)
      {
        $this->serviceContainer->getService('response')->addStylesheet($stylesheet);
      }
    }
    catch(Exception $e)
    {
      if (sfConfig::get('dm_debug') || sfConfig::get('dm_search_populating'))
      {
        throw $e;
      }
      elseif (sfConfig::get('sf_debug'))
      {
        $html = $this->helper->£link($this->page)
        ->currentSpan(false)
        ->param('dm_debug', 1)
        ->text(sprintf('[%s/%s] : %s', $widget['module'], $widget['action'], $e->getMessage()))
        ->title('Click to see the exception details')
        ->set('.dm_exception.s16.s16_error')
        ->render();
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
      
      $widgetWrapClass  = dmArray::toHtmlCssClasses(array('dm_widget', dmString::underscore($widget['module']), dmString::underscore($widget['action']), implode(' ', $widgetWrappedClasses)));
      $widgetInnerClass = dmArray::toHtmlCssClasses(array('dm_widget_inner', in_array($widget['module'].'.'.$widget['action'], self::$innerCssClassWidgets) ? '' : $widget['css_class']));
    }
    else
    {
      $widgetWrapClass  = 'dm_widget '.dmString::underscore($widget['module']).' '.dmString::underscore($widget['action']);
      $widgetInnerClass = 'dm_widget_inner';
    }
    
    return array($widgetWrapClass, $widgetInnerClass);
  }
  
  protected function getDocTypeOption($name, $default)
  {
    return dmArray::get(sfConfig::get('dm_html_doctype'), $name, $default);
  }
  
  protected function isHtml5()
  {
    return $this->isHtml5;
  }
}