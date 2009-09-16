<?php

class dmFrontPageEditHelper extends dmFrontPageHelper
{
	protected
	  $user;
	  
	public function setUser(dmUser $user)
	{
	  $this->user = $user;
	}

  public function renderZone(array $zone)
  {
    $style = (!$zone['width'] || $zone['width'] === '100%') ? '' : ' style="width: '.$zone['width'].';"';
    
    $html = '<div id="dm_zone_'.$zone['id'].'" class="'.dmArray::toHtmlCssClasses(array('dm_zone', $zone['css_class'])).'"'.$style.'>';

    if ($this->user && $this->user->can('zone_edit'))
    {
      $html .= '<a class="dm dm_zone_edit" title="'.$this->i18n->__('Edit this zone').'"></a>';
    }

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
    $html = '<div class="'.$widgetWrapClass.'" id="dm_widget_'.$widget['id'].'">';
    
    /*
     * Add edit button if required
     */
    if ($this->user && $this->user->can('widget_edit'))
    {
      $title = $this->i18n->__('Edit this widget').' '.$this->i18n->__($widget['module']).'.'.$this->i18n->__($widget['action']);
      
      $html .= '<a class="dm dm_widget_edit" title="'.$title.'"></a>';
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