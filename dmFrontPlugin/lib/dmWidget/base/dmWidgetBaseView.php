<?php

abstract class dmWidgetBaseView
{
  protected
  $context,
  $widgetType,
  $widget,
  $requiredVars = array(),
  $isIndexable = true;

  public function __construct(dmContext $context, dmWidgetType $type, array $data)
  {
    $this->context        = $context;
    
    $this->widgetType     = $type;
    $this->widget         = $data;

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
    return $this->renderPartial($vars);
  }
  
  protected function renderPartial(array $vars)
  {
    if ($this->widgetType->isCachable() && $this->context->getViewCacheManager())
    {
      $this->context->getViewCacheManager()->addCache($this->widget['module'], '_'.$this->widget['action'], array(
        'withLayout' => false,
        'lifeTime' => 86400,
        'clientLifeTime' => 86400,
        'contextual' => true,
        'vary' => array ()
      ));
    }
    
    return $this->doRenderPartial($vars);
  }
  
  abstract protected function doRenderPartial(array $vars);

  public function renderDefault()
  {
    if ($this->context->getUser()->can('widget_edit'))
    {
      $html = sprintf(
        '<div class="%s">%s %s.%s</div>',
        'dm dm_new_widget',
        $this->context->getI18n()->__('New widget'),
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

  public function renderForIndex(array $vars = array())
  {
    if ($this->isIndexable && $this->isValid())
    {
      $text = $this->doRenderForIndex($this->getViewVars($vars));
    }
    else
    {
      $text = '';
    }
    
    return $text;
  }

  protected function doRenderForIndex(array $vars)
  {
    return $this->doRender($vars);
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
      array('cssClass' => isset($this->widget['css_class']) ? $this->widget['css_class'] : null),
      (array) json_decode($this->widget['value']),
      dmString::toArray($vars)
    );
  }
}