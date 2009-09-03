<?php

class dmFrontPageEditHelper extends dmFrontPageHelper
{
	protected
	  $context;

  public function renderZone(array $zone)
  {
    $cssClasses = array('dm_zone', $zone['css_class']);

    $style = (!$zone['width'] || $zone['width'] === '100%') ? '' : " style='width: ".$zone['width'].";'";

    $html = sprintf(
      '<div class="%s" id="dm_zone_%d"%s>',
      dmArray::toHtmlCssClasses($cssClasses),
      $zone['id'],
      $style
    );

    if (dm::getUser()->can('zone_edit'))
    {
      $html .= sprintf(
        '<a class="dm dm_zone_edit" title="%s">%s</a>',
        dm::getI18n()->__('Edit this zone'),
        dm::getI18n()->__('Zone')
      );
    }

    $html .= '<div class="dm_widgets">';

    foreach(dmArray::get($zone, 'Widgets', array()) as $widget)
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
    $html = sprintf('<div class="%s" id="dm_widget_%d">', $widgetWrapClass, $widget['id']);

    /*
     * Add edit button if required
     */
    if (dm::getUser()->can('widget_edit'))
    {
      $html .= sprintf(
        '<a class="dm dm_widget_edit" title="%s">%s</a>',
        dm::getI18n()->__('Edit this widget').' '.dm::getI18n()->__($widget['module']).'.'.dm::getI18n()->__($widget['action']),
        dm::getI18n()->__('Widget')
      );
    }

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

}