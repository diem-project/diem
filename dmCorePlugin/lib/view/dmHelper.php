<?php

class dmHelper extends dmConfigurable
{
  protected
  $context,
  $serviceContainer;
  
  public function __construct(dmContext $context, array $options = array())
  {
    $this->context          = $context;
    $this->serviceContainer = $context->getServiceContainer();
    
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
    return $this->tag($tagName, $opt, false, false);
  }
  public function £o($tagName, array $opt = array())
  {
    return $this->open($tagName, $opt);
  }

  public function close($tagName)
  {
    if ($pos = strpos($tagName, '.') !== false)
    {
      $classes = substr($tagName, $pos+1);
      $tagName = substr($tagName, 0, $pos);
      
      if ($this->options['use_beaf'] && (strpos($classes, 'beafh') !== false || strpos($classes, 'beafv') !== false))
      {
        if (in_array($tagName, array('span', 'a', 'p')))
        {
          $beafTag = 'span';
        }
        else
        {
          $beafTag = 'div';
        }
        
        return '</'.$beafTag.'><'.$beafTag.' class="beafter"></'.$beafTag.'></'.$tagName.'>';
      }
    }
    
    return '</'.$tagName.'>';
  }
  public function £c($tagName)
  {
    return $this->close($tagName);
  }

  public function tag($tagName, $opt = array(), $content = false, $openAndClose = true)
  {
    if (!($tagName = trim($tagName)))
    {
      return '';
    }

    $tagOpt = array();

    // separate tag name from attribues in $tagName
    if ($firstSpacePos = strpos($tagName, ' '))
    {
      $tagNameOpt = substr($tagName, $firstSpacePos + 1);
      $tagName = substr($tagName, 0, $firstSpacePos);

      // DMS STYLE - string opt in name
      dmString::retrieveOptFromString($tagNameOpt, $tagOpt);
    }

    // JQUERY STYLE - css expression
    dmString::retrieveCssFromString($tagName, $tagOpt);

    // ARRAY STYLE - array opt
    if (is_array($opt) && !empty($opt))
    {
      if (isset($opt['json']))
      {
        $tagOpt['class'][] = json_encode($opt['json']);
        unset($opt['json']);
      }
      if (isset($opt['class']))
      {
        $tagOpt['class'][] = is_array($opt['class']) ? implode(' ', $opt['class']) : $opt['class'];
        unset($opt['class']);
      }
      
      $tagOpt = array_merge($tagOpt, $opt);
    }

    // SYMFONY STYLE - string opt
    elseif (is_string($opt) && $content)
    {
      $opt = sfToolkit::stringToArray($opt);
      if (isset($opt['class']))
      {
        $tagOpt['class'][] = explode(' ', $opt['class']);
        unset($opt['class']);
      }
      
      $tagOpt = array_merge($tagOpt, $opt);
    }

    if (!$content)
    {
      if (!is_array($opt))
      {
        $content = $opt;
      }
      else // No opt
      {
        $content = null;
      }
    }

    $class = isset($tagOpt['class']) ? $tagOpt['class'] : array();

    if ($this->options['use_beaf'] && (in_array('beafh', $class) || in_array('beafv', $class)))
    {
      $isBeaf = true;
      $tagOpt['class'][] = 'clearfix';
      $beafTag = in_array($tagName, array('span', 'a', 'p')) ? 'span' : 'div';
    }
    else
    {
      $isBeaf = false;
    }

    if(isset($tagOpt['lang']))
    {
      if($tagOpt['lang'] === $this->context->getUser()->getCulture())
      {
        unset($tagOpt['lang']);
      }
    }

    if (isset($tagOpt['class']) && is_array($tagOpt['class']))
    {
      $tagOpt['class'] = implode(' ', array_unique($tagOpt['class']));
    }

    $optHtml = '';
    foreach ($tagOpt as $key => $val)
    {
      $optHtml .= ' '.$key.'="'.htmlentities($val, ENT_COMPAT, 'UTF-8').'"';
    }

    if(in_array($tagName, $this->options['empty_elements']))
    {
      $tag = '<'.$tagName.$optHtml.' />';
    }
    elseif ($openAndClose)
    {
      if ($isBeaf)
      {
        $tag = '<'.$tagName.$optHtml.'><'.$beafTag.' class="beafore"></'.$beafTag.'><'.$beafTag.' class="beafin">'.$content.'</'.$beafTag.'><'.$beafTag.' class="beafter"></'.$beafTag.'></'.$tagName.'>';
      }
      else
      {
        $tag = '<'.$tagName.$optHtml.'>'.$content.'</'.$tagName.'>';
      }
    }
    else
    {
      if ($isBeaf)
      {
        $tag = '<'.$tagName.$optHtml.'><'.$beafTag.' class="beafore"></'.$beafTag.'><'.$beafTag.' class="beafin">';
      }
      else
      {
        $tag = '<'.$tagName.$optHtml.'>';
      }
    }

    return $tag;
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
  
  public function table()
  {
    return $this->serviceContainer->get('table_tag');
  }
  public function £table()
  {
    return $this->table();
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
}