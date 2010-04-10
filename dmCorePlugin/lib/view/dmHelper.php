<?php

require_once(sfConfig::get('dm_core_dir').'/lib/vendor/php-html-writer/lib/phpHtmlWriter.php');

class dmHelper extends dmConfigurable
{
  protected
  $context,
  $serviceContainer,
  $htmlWriter;
  
  public function __construct(dmContext $context, array $options = array())
  {
    $this->context          = $context;
    $this->serviceContainer = $context->getServiceContainer();
    $this->htmlWriter       = new phpHtmlWriter();
    
    $this->initialize($options);
  }

  public function initialize(array $options)
  {
    $this->configure($options);
  }
  
  public function getDefaultOptions()
  {
    return array(
      'use_beaf'        => false,
      'empty_elements'  => array('br', 'hr', 'img', 'input')
    );
  }
  
  public function renderPartial($moduleName, $actionName, $vars = array())
  {
    /*
     * partial -> _partial
     * dir/partial -> dir/partial
     */
    if (!strpos($actionName, '/'))
    {
      $actionName = '_'.$actionName;
    }

    $class = sfConfig::get('mod_'.strtolower($moduleName).'_partial_view_class', 'sf').'PartialView';
    $view = new $class($this->context, $moduleName, $actionName, '');
    $view->setPartialVars($vars);

    return $view->render();
  }

  public function renderComponent($moduleName, $componentName, $vars = array())
  {
    $this->context->getConfiguration()->loadHelpers('Partial');
    
    return get_component($moduleName, $componentName, $vars);
  }

  public function open($tagName, array $opt = array())
  {
    return $this->htmlWriter->open($tagName, $opt);
  }
  
  public function £o($tagName, array $opt = array())
  {
    return $this->open($tagName, $opt);
  }

  public function close($tagName)
  {
    return $this->htmlWriter->close($tagName);
  }
  
  public function £c($tagName)
  {
    return $this->close($tagName);
  }

  public function tag($tagName, $opt = array(), $content = false)
  {
    return $this->htmlWriter->tag($tagName, $opt, $content);
  }

  public function £($tagName, $opt = array(), $content = false, $openAndClose = true)
  {
    return $this->tag($tagName, $opt, $content, $openAndClose);
  }
  
  public function link($source = null)
  {
    return $this->serviceContainer->getService('link_tag_factory')->buildLink($source);
  }
  public function £link($source = null)
  {
    return $this->link($source);
  }
  
  public function media($source)
  {
    try
    {
      $this->serviceContainer->setParameter(
        'media_tag.source',
        $resource = $this->serviceContainer->getService('media_resource')->initialize($source)
      );
    }
    catch(Exception $e)
    {
      $this->context->getLogger()->err($e->getMessage());

      if (sfConfig::get('dm_debug'))
      {
        throw $e;
      }

      return $this->media(null);
    }
    
    $serviceName = 'media_tag_'.$resource->getMime();

    if (!$this->serviceContainer->hasService($serviceName))
    {
      throw new dmException('helper->media can not display '.$source.': missing service '.$serviceName);
    }

    if (!class_exists($this->serviceContainer->getParameter($serviceName.'.class')))
    {
      throw new dmException('helper->media can not display '.$source.': missing service '.$serviceName);
    }

    $media = $this->serviceContainer->getService($serviceName);

    foreach($media->getStylesheets() as $stylesheet)
    {
      $this->context->getResponse()->addStylesheet($stylesheet);
    }

    foreach($media->getJavascripts() as $javascript)
    {
      $this->context->getResponse()->addJavascript($javascript);
    }

    return $media;
  }
  public function £media($source)
  {
    return $this->media($source);
  }
  
  public function table($opt = null)
  {
    return $this->serviceContainer->get('table_tag')->set($opt);
  }
  public function £table($opt = null)
  {
    return $this->table($opt);
  }
  
  public function getStylesheetWebPath($asset)
  {
    return $this->context->getRequest()->getRelativeUrlRoot().$this->context->getResponse()->calculateAssetPath('css', $asset);
  }
  
  public function getStylesheetFullPath($asset)
  {
    return dmOs::join(sfConfig::get('sf_web_dir'), $this->context->getResponse()->calculateAssetPath('css', $asset));
  }
  
  public function getJavascriptWebPath($asset)
  {
    return $this->context->getRequest()->getRelativeUrlRoot().$this->context->getResponse()->calculateAssetPath('js', $asset);
  }
  
  public function getJavascriptFullPath($asset)
  {
    return dmOs::join(sfConfig::get('sf_web_dir'), $this->context->getResponse()->calculateAssetPath('js', $asset));
  }
  
  public function getOtherAssetWebPath($asset)
  {
    return $this->context->getRequest()->getRelativeUrlRoot().$this->context->getResponse()->calculateAssetPath('other', $asset);
  }

  public function __call($method, $arguments)
  {
    $event = new sfEvent($this, 'dm.helper.method_not_found', array('method' => $method, 'arguments' => $arguments));

    // calls all listeners until one is able to implement the $method
    $this->context->getEventDispatcher()->notifyUntil($event);

    // no listener was able to proces the event? The method does not exist
    if (!$event->isProcessed())
    {
      throw new sfException(sprintf('Call to undefined method %s::%s.', get_class($this), $method));
    }

    // return the listener returned value
    return $event->getReturnValue();
  }
  
}