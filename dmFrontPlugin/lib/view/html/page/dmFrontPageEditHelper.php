<?php

class dmFrontPageEditHelper extends dmFrontPageBaseHelper
{
  protected
  $user,
  $i18n,
  $widgetTypeManager;

  public function initialize(array $options)
  {
    parent::initialize($options);

    /*
     * Prepare some services for later access
     */
    $this->user = $this->serviceContainer->getService('user');
    $this->i18n = $this->serviceContainer->getService('i18n');
    $this->widgetTypeManager = $this->serviceContainer->getService('widget_type_manager');
    $this->moduleManager = $this->serviceContainer->getService('module_manager');
  }

  public function renderZone(array $zone)
  {
    $style = (!$zone['width'] || $zone['width'] === '100%') ? '' : ' style="width: '.$zone['width'].';"';
    
    $html = '<div id="dm_zone_'.$zone['id'].'" class="'.dmArray::toHtmlCssClasses(array('dm_zone', $zone['css_class'])).'"'.$style.'>';

    if ($this->user->can('zone_edit'))
    {
      $html .= '<a class="dm dm_zone_edit" title="'.$this->i18n->__('Edit this zone').'"></a>';
    }

    $html .= '<div class="dm_widgets">';

    $html .= $this->renderZoneInner($zone);

    $html .= '</div>';

    $html .= '</div>';

    return $html;
  }

  public function renderWidget(array $widget)
  {
    list($widgetWrapClass, $widgetInnerClass) = $this->getWidgetContainerClasses($widget);
    
    try
    {
      $widgetPublicName = $this->serviceContainer->getService('widget_type_manager')->getWidgetType($widget)->getPublicName();
    }
    catch(Exception $e)
    {
      $widgetPublicName = $widget['module'].'.'.$widget['action'];
    }

    /*
     * Open widget wrap with wrapped user's classes
     */
    $html = '<div class="'.$widgetWrapClass.'" id="dm_widget_'.$widget['id'].'">';

    /*
     * Add edit button if required
     */
    if ($this->user->can('widget_edit'))
    {
      $title = $this->i18n->__('Edit this %1%', array('%1%' => $this->i18n->__(dmString::lcfirst($widgetPublicName))));
      
      $html .= '<a class="dm dm_widget_edit" title="'.$title.'"></a>';
    }

    /*
     * Add record edit button if required
     */
    if('show' === $widget['action'] && $this->user->can('record_edit_front'))
    {
      $module = $this->moduleManager->getModule($widget['module']);

      if($module->hasModel())
      {
        $title = $this->i18n->__('Edit this %1%', array('%1%' => $this->i18n->__(dmString::lcfirst($module->getName()))));

        $html .= sprintf('<a class="dm dm_widget_record_edit" title="%s" data-widget_id="%s"></a>',
          $title,
          $widget['id']
        );
      }
    }

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

}