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

    /*
     * Open widget wrap with wrapped user's classes
     */
    $html = '<div class="'.$widgetWrapClass.'" id="dm_widget_'.$widget['id'].'">';

    /*
     * Add edit button if required
     */
    if ($this->user->can('widget_edit'))
    {
      try
      {
        $widgetPublicName = $this->serviceContainer->getService('widget_type_manager')->getWidgetType($widget)->getPublicName();
      }
      catch(Exception $e)
      {
        $widgetPublicName = $widget['module'].'.'.$widget['action'];
      }
      
      $title = $this->i18n->__('Edit this %1%', array('%1%' => $this->i18n->__(dmString::lcfirst($widgetPublicName))));
      
      $html .= '<a class="dm dm_widget_edit" title="'.$title.'"></a>';
    }

    /*
     * Add fast record edit button if required
     */
    if('show' === $widget['action'] && $this->user->can('widget_edit_fast') && $this->user->can('widget_edit_fast_record'))
    {
      if($module = $this->moduleManager->getModuleOrNull($widget['module']))
      {
        if($module->hasModel())
        {
          $html .= sprintf('<a class="dm dm_widget_record_edit" title="%s"></a>',
            $this->i18n->__('Edit this %1%', array('%1%' => $this->i18n->__(dmString::lcfirst($module->getName())))),
            $widget['id']
          );
        }
      }
    }

    /*
     * Add fast edit button if required
     */
    elseif(!$this->user->can('widget_edit') && $this->user->can('widget_edit_fast'))
    {
      $fastEditPermission = 'widget_edit_fast_'.dmString::underscore(str_replace('dmWidget', '', $widget['module'])).'_'.$widget['action'];

      if($this->user->can($fastEditPermission))
      {
        try
        {
          $widgetPublicName = $this->serviceContainer->getService('widget_type_manager')->getWidgetType($widget)->getPublicName();
        }
        catch(Exception $e)
        {
          $widgetPublicName = $widget['module'].'.'.$widget['action'];
        }

        $html .= sprintf('<a class="dm dm_widget_fast_edit" title="%s"></a>',
          $this->i18n->__('Edit this %1%', array('%1%' => $this->i18n->__(dmString::lcfirst($widgetPublicName)))),
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