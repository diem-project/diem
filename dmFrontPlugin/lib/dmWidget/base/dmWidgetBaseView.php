<?php

abstract class dmWidgetBaseView
{
  protected
  $context,
  $widgetType,
  $widget,
  $requiredVars = array(),
  $isIndexable = true,
  $vars,
  $javascripts = array(),
  $stylesheets = array();

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
  
  protected function addJavascript($keys)
  {
    $this->javascripts = array_merge($this->javascripts, (array) $keys);

    return $this;
  }
  
  public function getJavascripts()
  {
    return $this->javascripts;
  }
  
  protected function addStylesheet($keys)
  {
    $this->stylesheets = array_merge($this->stylesheets, (array) $keys);

    return $this;
  }
  
  public function getStylesheets()
  {
    return $this->stylesheets;
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
  
  protected function compileVars(array $vars = array())
  {
    $this->compiledVars = array_merge(
      array('cssClass' => isset($this->widget['css_class']) ? $this->widget['css_class'] : null),
      (array) json_decode((string) $this->widget['value'], true),
      dmString::toArray($vars)
    );
  }

  public function render(array $vars = array())
  {
    $this->compileVars($vars);
    
    if ($this->isValid())
    {
      $html = $this->doRender();
    }
    else
    {
      $html = $this->renderDefault();
    }
    
    return $html;
  }
  
  protected function doRender()
  {
    return $this->renderPartial($this->filterViewVars($this->compiledVars));
  }
  
  protected function renderPartial(array $vars)
  {
    if ($this->isCachable() && $this->context->getViewCacheManager())
    {
      $this->context->getViewCacheManager()->addCache($this->widget['module'], '_'.$this->widget['action'], array(
        'withLayout'      => false,
        'lifeTime'        => 86400,
        'clientLifeTime'  => 86400,
        'contextual'      => !$this->isStatic(),
        'vary'            => array($this->widget['id'])
      ));
    }

    // add dm_widget to the component/partial vars
    $vars['dm_widget'] = $this->widget;
    
    return $this->doRenderPartial($vars);
  }
  
  abstract protected function doRenderPartial(array $vars);

  public function renderDefault()
  {
    if ($this->context->getUser()->can('widget_edit'))
    {
      $html = sprintf(
        '<div class="%s">%s %s</div>',
        'dm dm_new_widget',
        $this->__('New widget'),
        $this->__($this->widgetType->getPublicName())
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
    $this->compileVars($vars);
    
    if ($this->isIndexable && $this->isValid())
    {
      $text = $this->doRenderForIndex();
    }
    else
    {
      $text = '';
    }
    
    return $text;
  }

  protected function doRenderForIndex()
  {
    return $this->doRender();
  }
  
  protected function isValid()
  {
    foreach($this->getRequiredVars() as $requiredVar)
    {
      if (!isset($this->compiledVars[$requiredVar]))
      {
        return false;
      }
    }

    return true;
  }
  
  public function getViewVars()
  {
    if (!is_array($this->compiledVars))
    {
      throw new dmException('View vars have not been compiled yet');
    }
    
    return $this->filterViewVars($this->compiledVars);
  }
  
  protected function filterViewVars(array $vars = array())
  {
    return $vars;
  }
  
  public function isCachable()
  {
    return sfConfig::get('sf_cache') && $this->widgetType->isCachable();
  }
  
  public function isStatic()
  {
    return $this->widgetType->isStatic();
  }
  
  public function getCache()
  {
    return $this->getService('cache_manager')->getCache($this->getCacheName())->get($this->generateCacheKey());
  }
  
  public function setCache($html)
  {
    return $this->getService('cache_manager')->getCache($this->getCacheName())->set($this->generateCacheKey(), $html, 86400);
  }
  
  protected function getCacheName()
  {
    return sprintf('%s/%s/template', sfConfig::get('sf_app'), sfConfig::get('sf_environment'));
  }
  
  protected function generateCacheKey()
  {
    return sprintf('widget/%s/%s/%s', $this->widget['module'], $this->widget['action'], md5(serialize($this->filterCacheVars($this->compiledVars))));
  }
  
  protected function filterCacheVars(array $vars)
  {
    if (!$this->isStatic() && $this->context->getPage())
    {
      $vars['page_id'] = $this->context->getPage()->get('id');
    }
    
    return $vars;
  }

  protected function getHelper()
  {
    return $this->context->getHelper();
  }

  protected function getService($name, $class = null)
  {
    return $this->context->get($name, $class);
  }
  
  protected function __($message, $arguments = array(), $catalogue = null)
  {
    return $this->context->getI18n()->__($message, $arguments, $catalogue);
  }
}