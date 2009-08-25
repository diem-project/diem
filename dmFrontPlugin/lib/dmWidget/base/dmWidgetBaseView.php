<?php

abstract class dmWidgetBaseView
{

	protected
	$widget,
	$widgetType,
	$requiredVars = array();

	public function __construct(array $widget)
	{
    $this->widget = $widget;
    $this->widgetType = dmWidgetTypeManager::getWidgetType($widget['module'], $widget['action']);

    $this->configure();
	}

	protected function configure()
	{

	}

  public function getRequiredVars()
  {
  	return array_unique($this->requiredVars);
  }

  public function addRequiredVar($var)
  {
  	if (is_array($var))
  	{
  		$this->requiredVars = array_merge($this->requiredVars, $var);
  	}
  	else
  	{
  		$this->requiredVars[] = $var;
  	}
  }

  abstract public function render();

	public function renderDefault()
	{
    if (dm::getUser()->can('widget_edit'))
    {
      return sprintf(
        '<div class="%s">%s %s.%s</div>',
        'dm dm_new_widget',
        dm::getI18n()->__('New widget'),
        $this->widget['module'],
        $this->widget['action']
      );
    }
	}

  public function isValid()
  {
  	$viewVars = (array) json_decode($this->widget['value']);

    foreach($this->getRequiredVars() as $requiredVar)
    {
      if (!isset($viewVars[$requiredVar]))
      {
      	return false;
      }
    }

    return true;
  }

	public function getViewVars($vars = array())
	{
		return array_merge(
		  array('cssClass' => $this->widget['css_class']),
		  (array) json_decode($this->widget['value']),
		  dmString::toArray($vars)
		);
	}

}