<?php

abstract class dmFrontPageBaseHelper extends dmConfigurable
{
  protected
    $dispatcher,
    $serviceContainer,
    $helper,
    $page,
    $areas;

  protected static
  $innerCssClassWidgets = array('dmWidgetContent.title', 'dmWidgetContent.media', 'dmWidgetContent.link');
    
  public function __construct(sfEventDispatcher $dispatcher, sfServiceContainer $serviceContainer, dmHelper $helper, array $options = array())
  {
    $this->dispatcher        = $dispatcher;
    $this->serviceContainer  = $serviceContainer;
    $this->helper            = $helper;
    
    $this->initialize($options);
  }
  
  public function getDefaultOptions()
  {
    return array(
      'widget_css_class_pattern'  => '%module%_%action%',
      'is_html5'                  => 5 == $this->getDocTypeOption('version', 5)
    );
  }
  
  public function initialize(array $options)
  {
    $this->configure($options);
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
  
  public function getAreas($culture = null)
  {
    if (null === $this->areas)
    {
      if (!$this->page instanceof DmPage)
      {
        throw new dmException('Can not fetch page areas because no page have been set');
      }
      
      $culture = null === $culture ? $this->serviceContainer->getParameter('user.culture') : $culture;
      $fallBackCulture = sfConfig::get('sf_default_culture');
      
      $this->areas = dmDb::query('DmArea a INDEXBY a.type')
      ->leftJoin('a.Zones z')
      ->leftJoin('z.Widgets w')
      ->leftJoin('w.Translation wTranslation WITH wTranslation.lang = ? OR wTranslation.lang = ?', array($culture, $fallBackCulture))
      ->select('a.type, z.width, z.css_class, w.module, w.action, wTranslation.value, w.css_class')
      ->where('a.dm_layout_id = ?', $this->page->getPageView()->getLayout()->get('id'))
      ->orWhere('a.dm_page_view_id = ?', $this->page->getPageView()->get('id'))
      ->orderBy('z.position asc, w.position asc')
      ->fetchArray();
      
      /*
       * WARNING strange code
       * This code is to simulate widget i18n fallback,
       * which can not be achived
       * normally when hydrating with an array
       */
      foreach($this->areas as $areaIndex => $area)
      {
        foreach($area['Zones'] as $zoneIndex => $zone)
        {
          foreach($zone['Widgets'] as $widgetIndex => $widget)
          {
            $value = null;
            
            // there is a translation for $culture
            if (isset($widget['Translation'][$culture]))
            {
              $value = $widget['Translation'][$culture]['value'];
            }
            // there is a default translation for $fallBackCulture
            elseif (isset($widget['Translation'][$fallBackCulture]))
            {
              $value = $widget['Translation'][$fallBackCulture]['value'];
            }
            
            // assign the value to the widget array
            $this->areas[$areaIndex]['Zones'][$zoneIndex]['Widgets'][$widgetIndex]['value'] = $value;
            
            // unset the useless Translation array
            unset($this->areas[$areaIndex]['Zones'][$zoneIndex]['Widgets'][$widgetIndex]['Translation']);
          }
        }
      }
      /*
       * End of strange code
       */
    }
    
    return $this->areas;
  }
  
  public function getArea($type)
  {
    $areas = $this->getAreas();

    if (!isset($areas[$type]))
    {
      throw new dmException(sprintf('Page %s with layout %s has no area for type %s', $this->page, $this->page->Layout, $type));
    }

    return $areas[$type];
  }

  public function renderAccessLinks()
  {
    if (!sfConfig::get('dm_accessibility_access_links', true))
    {
      return '';
    }

    $html = '<div class="dm_access_links">';

    $html .= '<a href="#content">'.$this->serviceContainer->getService('i18n')->__('Go to content').'</a>';

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
     * Add a content id for accessibility purpose ( access link )
     */
    if ('content' === $type)
    {
      $html .= '<div id="dm_content">';
    }
    
    $html .= $this->helper->£o($tagName, $options);

    $html .= '<div class="dm_zones clearfix">';

    $html .= $this->renderAreaInner($area);

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
  
  protected function renderAreaInner(array $area)
  {
    $html = '';
    
    if (!empty($area['Zones']))
    {
      foreach($area['Zones'] as $zone)
      {
        $html .= $this->renderZone($zone);
      }
    }
    
    return $html;
  }
  
  /*
   * get a tag name for a given area, depending on the document type
   */
  protected function getAreaTypeTagName($areaType)
  {
    if ($this->isHtml5())
    {
      switch($areaType)
      {
        case 'top':     $tagName = 'header';  break;
        case 'left':    $tagName = 'aside';   break;
        case 'content': $tagName = 'section'; break;
        case 'right':   $tagName = 'aside';   break;
        case 'bottom':  $tagName = 'footer';  break;
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
    
    $html .= $this->renderZoneInner($zone);

    $html .= '</div>';

    $html .= '</div>';

    return $html;
  }
  
  protected function renderZoneInner(array $zone)
  {
    $html = '';
    
    if(!empty($zone['Widgets']))
    {
      foreach($zone['Widgets'] as $widget)
      {
        $html .= $this->renderWidget($widget);
      }
    }
    
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
      if (sfConfig::get('dm_debug') || sfConfig::get('dm_search_populating') || 'test' === sfConfig::get('sf_environment'))
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
    // class for the widget div wrapper
    $widgetWrapClass = trim('dm_widget '.$this->getWidgetCssClassFromPattern($widget));
    
    // if no user css_class, or if the user css_class must be applied inside the widget only
    if(empty($widget['css_class']) || $this->isInnerCssClassWidget($widget))
    {
      $widgetInnerClass = 'dm_widget_inner';
    }
    else
    {
      $widgetInnerClass = 'dm_widget_inner '.$widget['css_class'];
    }
    
    return array($widgetWrapClass, $widgetInnerClass);
  }
  
  /*
   * Must this widget's user css_class only be applied inside the widget ?
   * @return bool whether the css_class is applied inside
   */
  protected function isInnerCssClassWidget(array $widget)
  {
    return in_array($widget['module'].'.'.$widget['action'], self::$innerCssClassWidgets);
  }
  
  protected function getWidgetCssClassFromPattern(array $widget)
  {
    $pattern = $this->getOption('widget_css_class_pattern');
    
    if (empty($pattern))
    {
      return '';
    }
    
    return strtr($pattern, array(
      '%module%' => dmString::underscore($widget['module']),
      '%action%' => dmString::underscore($widget['action'])
    ));
  }
  
  protected function getDocTypeOption($name, $default)
  {
    return dmArray::get(sfConfig::get('dm_html_doctype'), $name, $default);
  }
  
  protected function isHtml5()
  {
    return $this->getOption('is_html5');
  }
}