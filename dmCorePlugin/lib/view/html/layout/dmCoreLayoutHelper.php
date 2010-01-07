<?php

class dmCoreLayoutHelper
{
  protected
    $dispatcher,
    $serviceContainer,
    $baseWebPath,
    $isHtml5;

  public function __construct(sfEventDispatcher $dispatcher, dmBaseServiceContainer $serviceContainer)
  {
    $this->dispatcher = $dispatcher;
    $this->serviceContainer = $serviceContainer;
    
    $this->initialize();
  }
  
  protected function initialize()
  {
    $this->isHtml5 = 5 == $this->getDocTypeOption('version', 5);
  }
  
  public function renderHead()
  {
    return
    $this->renderHttpMetas().
    $this->renderMetas().
    $this->renderStylesheets().
    $this->renderFavicon().
    $this->renderIeHtml5Fix();
  }

  public function renderBodyTag()
  {
    return sprintf('<body class="%s_%s">',
      $this->serviceContainer->getParameter('controller.module'),
      $this->serviceContainer->getParameter('controller.action')
    );
  }
  
  protected function getDocTypeOption($name, $default)
  {
    $value = dmArray::get(sfConfig::get('dm_html_doctype'), $name, $default);
    
    if ('version' === $name && 1 == $value)
    {
      $value = '1.0';
    }
    
    return $value;
  }

  protected function isHtml5()
  {
    return $this->isHtml5;
  }

  public function renderDoctype()
  {
    if ($this->isHtml5())
    {
      $doctype = '<!DOCTYPE html>';
    }
    else
    {
      $doctype = sprintf(
        '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML %s %s//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-%s.dtd">',
        $this->getDocTypeOption('version', '1.0'),
        ucfirst(strtolower($this->getDocTypeOption('compliance', 'transitional'))),
        strtolower($this->getDocTypeOption('compliance', 'transitional'))
      );
    }
    
    return $doctype."\n";
  }
  
  public function renderHtmlTag()
  {
    if ($this->isHtml5())
    {
      return '<html>';
    }
    
    $culture = $this->serviceContainer->getParameter('user.culture');

    return sprintf(
      '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="%s" lang="%s">',
      $culture,
      $culture
    );
  }
  
  
  protected function getMetas()
  {
    return array(
      'title'       => $this->serviceContainer->getService('response')->getTitle(),
      'language'    => $this->serviceContainer->getParameter('user.culture'),
    );
  }
  
  public function renderMetas()
  {
    /*
     * Allow listeners of dm.response.filter_metas event
     * to filter and modify the metas list
     */
    $metas = $this->dispatcher->filter(
      new sfEvent($this, 'dm.layout.filter_metas'),
      $this->getMetas()
    )->getReturnValue();
    
    $metasHtml = '';
    foreach( $metas as $key => $value)
    {
      $value = htmlentities($value, ENT_COMPAT, 'UTF-8');
      if ('title' === $key)
      {
        $metasHtml .= '<title>'.$value.'</title>'."\n";
      }
      else
      {
        $metasHtml .= '<meta name="'.$key.'" content="'.$value.'" />'."\n";
      }
    }

    return $metasHtml;
  }
  
  public function renderHttpMetas()
  {
    $httpMetas = $this->serviceContainer->getService('response')->getHttpMetas();
    
    $html = '';
    
    foreach($httpMetas as $httpequiv => $value)
    {
      $html .= '<meta http-equiv="'.$httpequiv.'" content="'.$value.'" />'."\n";
    }

    return $html;
  }

  public function renderStylesheets()
  {
    /*
     * Allow listeners of dm.layout.filter_stylesheets event
     * to filter and modify the stylesheets list
     */
    $stylesheets = $this->dispatcher->filter(
      new sfEvent($this, 'dm.layout.filter_stylesheets'),
      $this->serviceContainer->getService('response')->getStylesheets()
    )->getReturnValue();
    
    $relativeUrlRoot = dmArray::get($this->serviceContainer->getParameter('request.context'), 'relative_url_root');

    $html = '';
    foreach ($stylesheets as $file => $options)
    {
      $stylesheetTag = '<link rel="stylesheet" type="text/css" media="'.dmArray::get($options, 'media', 'all').'" href="'.$relativeUrlRoot.$file.'" />';
    
      if (isset($options['condition']))
      {
        $stylesheetTag = sprintf('<!--[if %s]>%s<![endif]-->', $options['condition'], $stylesheetTag);
      }
      
      $html .= $stylesheetTag."\n";
    }
    
    sfConfig::set('symfony.asset.stylesheets_included', true);
  
    return $html;
  }
  
  public function renderJavascripts()
  {
    /*
     * Allow listeners of dm.layout.filter_javascripts event
     * to filter and modify the javascripts list
     */
    $javascripts = $this->dispatcher->filter(
      new sfEvent($this, 'dm.layout.filter_javascripts'),
      $this->serviceContainer->getService('response')->getJavascripts()
    )->getReturnValue();
    
    sfConfig::set('symfony.asset.javascripts_included', true);
    
    $relativeUrlRoot = dmArray::get($this->serviceContainer->getParameter('request.context'), 'relative_url_root');
    
    $html = '';
    foreach ($javascripts as $file => $options)
    {
      $html .= '<script type="text/javascript" src="'.($file{0} === '/' ? $relativeUrlRoot.$file : $file).'"></script>';
    }
  
    return $html;
  }

  public function renderIeHtml5Fix()
  {
    if ($this->isHtml5())
    {
      return '<!--[if IE]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->';
    }
    
    return '';
  }
  
  protected function getJavascriptConfig()
  {
    $requestContext = $this->serviceContainer->getParameter('request.context');
    
    return array_merge($this->serviceContainer->getService('response')->getJavascriptConfig(), array(
      'relative_url_root'  => $requestContext['relative_url_root'],
      'dm_core_asset_root' => $requestContext['relative_url_root'].'/'.sfConfig::get('dm_core_asset').'/',
      'script_name'        => sfConfig::get('sf_no_script_name') ? $requestContext['relative_url_root'].'/' : $requestContext['script_name'].'/',
      'debug'              => sfConfig::get('sf_debug') ? true : false,
      'culture'            => $this->serviceContainer->getParameter('user.culture'),
      'module'             => $this->serviceContainer->getParameter('controller.module')
    ));
  }
  
  public function renderJavascriptConfig()
  {
    return '<script type="text/javascript">var dm_configuration = '.json_encode($this->getJavascriptConfig()).';</script>';
  }

  
  protected function getFavicon()
  {
    foreach(array('ico', 'png', 'gif') as $extension)
    {
      if (file_exists(sfConfig::get('sf_web_dir').'/favicon.'.$extension))
      {
        return 'favicon.'.$extension;
      }
    }
  }
  
  public function renderFavicon()
  {
    $favicon = $this->getFavicon();

    if ($favicon)
    {
      return sprintf('<link rel="shortcut icon" href="%s/%s" type="%s" />',
        dmArray::get($this->serviceContainer->getParameter('request.context'), 'relative_url_root'),
        $favicon,
        'image/x-icon'
      )."\n";
    }

    return '';
  }

  protected function getHelper()
  {
    return $this->serviceContainer->getService('helper');
  }
}