<?php

class dmFrontPageEditHelper extends dmFrontPageBaseHelper
{
  protected
  $user,
  $i18n,
  $widgetTypeManager,
  $behaviorsManager;

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
    $this->behaviorsManager = $this->serviceContainer->getService('behaviors_manager');
  }

  public function renderArea($name, $options = array())
  {
    $options = dmString::toArray($options);

    //Set id of page we need
    $this->global_area_id = dmArray::get($options, 'global_area', null);

    unset($options['global_area']);

    $tagName = $this->getAreaTypeTagName($name);

    $area = $this->getArea($name);

    list($prefix, $type) = explode('.', $name);
    
    $options['class'] = array_merge(dmArray::get($options, 'class', array()), array(
'dm_area',
'dm_'.$prefix.'_'.$type,
'dm_area_'.$area['id'],
($prefix == 'layout') ? 'dm_layout_shared' : '',
($this->behaviorsManager->isAreaAttachable()) ? 'dm_behaviors_attachable' : ''
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

    $html .= $this->helper->open($tagName, $options);

    if ($this->behaviorsManager->isAreaAttachable()){
        $html .= '<a class="dm dm_area_edit' . (($this->user->can('behavior_add')) ? ' dm_behaviors_droppable' : '') . '">' . (('content' === $type) ? $this->i18n->__('Content') : $this->i18n->__('Layout')) . '</a>';

        if ($this->user->can('behavior_edit') || $this->user->can('behavior_delete')) 
        {
          $html .= '<a class="dm dm_edit_behaviors_icon dm_edit_behaviors_area_icon s16_gear s16" title="'.$this->i18n->__('Edit behaviors').'"></a>';
        }
    }
    
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
  
  public function renderZone(array $zone)
  {
    $style = (!$zone['width'] || $zone['width'] === '100%') ? '' : ' style="width: '.$zone['width'].';"';
    
    $html = '<div id="dm_zone_'.$zone['id'].'" class="'.dmArray::toHtmlCssClasses(array(
        'dm_zone', 
        'dm_zone_'.$zone['id'],
        $zone['css_class'],
        $this->behaviorsManager->isZoneAttachable() ? 'dm_behaviors_attachable' : ''
        )).'"'.$style.'>';

    if ($this->user->can('zone_edit'))
    {
      $html .= '<a class="dm dm_zone_edit' . (($this->user->can('behavior_add') && $this->behaviorsManager->isZoneAttachable()) ? ' dm_behaviors_droppable' : '') . '" title="'.$this->i18n->__('Edit this zone').'"></a>';
    }
    
    if (($this->user->can('behavior_edit') || $this->user->can('behavior_delete')) && $this->behaviorsManager->isZoneAttachable()) 
    {
      $html .= '<a class="dm dm_edit_behaviors_icon s16_gear s16" title="'.$this->i18n->__('Edit behaviors').'"></a>';
    }
    
    $html .= '<div class="dm_widgets">';

    $html .= $this->renderZoneInner($zone);

    $html .= '</div>';

    $html .= '</div>';

    return $html;
  }

  public function renderWidget(array $widget, $executeWidgetAction = false)
  {
    if ($executeWidgetAction)
    {
      $this->executeWidgetAction($widget);
    }
    
    //it the widget is called programmatically, it has no id and can not be edited
    if(!isset($widget['id']))
    {
      $widget['id'] = 'programmatically_'.substr(md5(serialize($widget)), 0, 6); // give a unique id
      $is_programmatically = true;
    }
    else
    {
      $is_programmatically = false;
    }

    list($widgetWrapClass, $widgetInnerClass) = $this->getWidgetContainerClasses($widget);

    /*
     * Open widget wrap with wrapped user's classes
     */
    $html = '<div class="'.$widgetWrapClass.' dm_widget_'.$widget['id'].' dm_behaviors_attachable" id="dm_widget_'.$widget['id'].'">';

    if (!$is_programmatically)
    {
    
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

        $title = $this->i18n->__('Edit this %1%', array('%1%' => $this->i18n->__($widgetPublicName)));

        $html .= '<a class="dm dm_widget_edit' . (($this->user->can('behavior_add') && $this->behaviorsManager->isWidgetAttachable()) ? ' dm_behaviors_droppable' : '') . '" title="'.htmlentities($title, ENT_COMPAT, 'UTF-8').'"></a>';
      }

      if (($this->user->can('behavior_edit') || $this->user->can('behavior_delete')) && $this->behaviorsManager->isWidgetAttachable())
      {
        $html .= '<a class="dm dm_edit_behaviors_icon s16_gear s16" title="'.$this->i18n->__('Edit behaviors').'"></a>';
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
              $this->i18n->__('Edit this %1%', array('%1%' => $this->i18n->__($module->getName()))),
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