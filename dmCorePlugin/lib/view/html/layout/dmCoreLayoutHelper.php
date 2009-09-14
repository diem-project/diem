<?php

abstract class dmCoreLayoutHelper
{
	protected
	  $dispatcher,
	  $user,
    $request,
    $response,
    $actionStack,
    $helper,
	  $theme,
	  $baseWebPath,
	  $isHtml5;

  public function __construct(sfEventDispatcher $dispatcher, dmUser $user, dmWebRequest $request, dmWebResponse $response, sfActionStack $actionStack, dmOoHelper $helper)
  {
    $this->dispatcher = $dispatcher;
    $this->user       = $user;
    $this->request    = $request;
    $this->response   = $response;
    $this->actionStack = $actionStack;
    $this->helper     = $helper;
    
    $this->initialize();
  }
  
  protected function initialize()
  {
    $this->theme    = $this->user->getTheme();
    $this->isHtml5  = sfConfig::get('dm_html_doctype_version', 5) == 5;
    $this->relativeUrlRoot = $this->request->getRelativeUrlRoot();
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
  	
    $culture = $this->user->getCulture();

    return sprintf(
      '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="%s" lang="%s">',
      $culture,
      $culture
    );
  }
  
  public function renderHttpMetas()
  {
    $httpMetas = $this->response->getHttpMetas();
    
    $html = '';
    
//    if ($this->isHtml5())
//    {
//      $html .= sprintf('<meta charset="%s">', strtoupper($this->response->getCharset()));
//      
//      if(isset($httpMetas['Content-Type']))
//      {
//        unset($httpMetas['Content-Type']);
//      }
//    }
    
    foreach($httpMetas as $httpequiv => $value)
    {
      $html .= sprintf('<meta http-equiv="%s" content="%s" />', $httpequiv, $value);
    }

    return $html;
  }

  public function renderStylesheets()
  {
    /*
     * Allow listeners of dm.response.filter_stylesheets event
     * to filter and modify the stylesheets list
     */
    $stylesheets = $this->dispatcher->filter(
      new sfEvent($this, 'dm.response.filter_stylesheets'),
      $this->response->getStylesheets()
    )->getReturnValue();
  
    $html = '';
    foreach ($stylesheets as $file => $options)
    {
      $html .= "\n".'<link rel="stylesheet" type="text/css" media="screen" href="'.$this->relativeUrlRoot.$file.'" />';
    }
    
    sfConfig::set('symfony.asset.stylesheets_included', true);
  
    return $html;
  }
  
  public function renderJavascripts()
  {
    /*
     * Allow listeners of dm.response.filter_javascripts event
     * to filter and modify the javascripts list
     */
    $javascripts = $this->dispatcher->filter(
      new sfEvent($this, 'dm.response.filter_javascripts'),
      $this->response->getJavascripts()
    )->getReturnValue();
    
    sfConfig::set('symfony.asset.javascripts_included', true);
    
    $html = '';
    foreach ($javascripts as $file => $options)
    {
      $html .= '<script type="text/javascript" src="'.($file{0} === '/' ? $this->relativeUrlRoot.$file : $file).'"></script>';
    }
  
    return $html;
  }
  
  protected function getJavascriptConfig()
  {
  	return array_merge($this->response->getJavascriptConfig(), array(
  	  'relative_url_root'  => $this->relativeUrlRoot,
      'dm_core_asset_root' => $this->relativeUrlRoot.'/'.sfConfig::get('dm_core_asset').'/',
      'script_name'        => $this->request->getScriptName().'/',
  	  'debug'              => sfConfig::get('sf_debug') ? 'true' : 'false',
      'culture'            => $this->user->getCulture(),
  	  'module'             => $this->actionStack->getLastEntry()->getModuleName()
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
      return '<link rel="shortcut icon" href="'.$this->relativeUrlRoot.'/'.$favicon.'" />';
    }

    return '';
  }

}