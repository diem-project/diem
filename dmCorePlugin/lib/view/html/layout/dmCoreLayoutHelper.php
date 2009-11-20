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
    $this->isHtml5  = sfConfig::get('dm_html_doctype_version', 5) == 5;
  }

  public function renderBodyTag()
  {
    return sprintf('<body class="%s_%s">',
      $this->serviceContainer->getParameter('controller.module'),
      $this->serviceContainer->getParameter('controller.action')
    );
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
        sfConfig::get('dm_html_doctype_version', '1.0'),
        ucfirst(strtolower(sfConfig::get('dm_html_doctype_compliance', 'transitional'))),
        strtolower(sfConfig::get('dm_html_doctype_compliance', 'transitional'))
      );
    }
    
    return $doctype;
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
      $value = htmlentities($value);
      if ('title' === $key)
      {
        $metasHtml = "\n".'<title>'.$value.'</title>';
      }
      else
      {
        $metasHtml .= "\n".'<meta name="'.$key.'" content="'.$value.'" />';
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
      $html .= '<meta http-equiv="'.$httpequiv.'" content="'.$value.'" />';
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
      $html .= "\n".'<link rel="stylesheet" type="text/css" media="'.dmArray::get($options, 'media', 'all').'" href="'.$relativeUrlRoot.$file.'" />';
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
  
  protected function getJavascriptConfig()
  {
    $requestContext = $this->serviceContainer->getParameter('request.context');
    
    return array_merge($this->serviceContainer->getService('response')->getJavascriptConfig(), array(
      'relative_url_root'  => $requestContext['relative_url_root'],
      'dm_core_asset_root' => $requestContext['relative_url_root'].'/'.sfConfig::get('dm_core_asset').'/',
      'script_name'        => $requestContext['script_name'].'/',
      'debug'              => sfConfig::get('sf_debug') ? 'true' : 'false',
      'culture'            => $this->serviceContainer->getParameter('user.culture'),
      'module'             => $this->serviceContainer->getParameter('controller.module')
    ));
  }
  
  public function renderJavascriptConfig()
  {
    return '<script type="text/javascript">var dm_configuration = '.json_encode($this->getJavascriptConfig()).';</script>';
  }

  public function renderFavicon()
  {
    if (is_readable(sfConfig::get('sf_web_dir').'/favicon.ico'))
    {
      $favicon = 'favicon.ico';
    }
    elseif (is_readable(sfConfig::get('sf_web_dir').'/images/favicon.png'))
    {
      $favicon = 'images/favicon.png';
    }
    elseif (is_readable(sfConfig::get('sf_web_dir').'/images/favicon.gif'))
    {
      $favicon = 'images/favicon.gif';
    }

    if (isset($favicon))
    {
      return "\n".'<link rel="shortcut icon" href="'.dmArray::get($this->serviceContainer->getParameter('request.context'), 'relative_url_root').'/'.$favicon.'" />';
    }

    return '';
  }

  protected function getHelper()
  {
    return $this->serviceContainer->getService('helper');
  }
}