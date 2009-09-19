<?php

abstract class dmWidgetBaseView
{

  protected
  $dispatcher,
  $helper,
  $widgetType,
  $widget,
  $requiredVars = array();

  public function __construct(sfEventDispatcher $dispatcher, dmHelper $helper, dmWidgetType $type, array $data)
  {
    $this->dispatcher        = $dispatcher;
    $this->helper            = $helper;
    $this->widgetType        = $type;
    $this->widget            = $data;

    $this->configure();
  }

  protected function configure()
  {

  }

  public function getRequiredVars()
  {
    return $this->requiredVars;
  }
  
  public function isRequiredVar($var)
  {
    return in_array($var, $this->getRequiredVars());
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
    
    $this->requiredVars = array_unique($this->requiredVars);
  }
  
  public function removeRequiredVar($var)
  {
    if (is_array($var))
    {
      foreach($var as $v)
      {
        $this->removeRequiredVar($v);
      }
    }
    elseif (false !== ($varIndex = array_search($var, $this->requiredVars)))
    {
      unset($this->requiredVars[$varIndex]);
    }
  }

  
  public function render(array $vars = array())
  {
    if ($this->isValid())
    {
      $html = $this->doRender($this->getViewVars($vars));
    }
    else
    {
      $html = $this->renderDefault();
    }
    
    return $html;
  }
  
  protected function doRender(array $vars)
  {
    return $this->doRenderPartial($vars);
  }
  
  abstract protected function doRenderPartial(array $vars);

  public function renderDefault()
  {
    if (dm::getUser()->can('widget_edit'))
    {
      $html = sprintf(
        '<div class="%s">%s %s.%s</div>',
        'dm dm_new_widget',
        dm::getI18n()->__('New widget'),
        $this->widget['module'],
        $this->widget['action']
      );
    }
    else
    {
      $html = '';
    }
    
    return $html;
  }

  public function toIndexableString(array $vars)
  {
    return $this->render($vars);
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

  public function getViewVars(array $vars = array())
  {
    return array_merge(
      array('cssClass' => $this->widget['css_class']),
      (array) json_decode($this->widget['value']),
      dmString::toArray($vars)
    );
  }

}