<?php

class dmAdminLinkTag extends dmLinkTag
{

  public static function build($source = null)
  {
    if (null === $source)
    {
      $source = dm::getRequest()->getScriptName();
    }
    $object = new self($source);
    return $object;
  }

  public function __construct($source)
  {
    $this->set('source', $source)->addClass('link');
  }

  protected function getBaseHref()
  {
  	$source = $this->get('source');

    if(is_string($source))
    {
      if (strncmp($source, 'app:', 4) === 0)
      {
        $type = 'uri';
        $app = substr($source, 4);
        /*
         * A slug may be added to the app name, extract it
         */
        if ($slashPos = strpos($app, '/'))
        {
          $slug = substr($app, $slashPos);
          $app  = substr($app, 0, $slashPos);
        }
        else
        {
          $slug = '';
        }
        $source = dmContext::getInstance()->getAppUrl($app).$slug;
      }
    	elseif ($source{0} === '/')
    	{
    		/*
    		 * add relativeUrlRoot to absolute resource
    		 */
    		if($relativeUrlRoot = dm::getRequest()->getRelativeUrlRoot())
    		{
	    		if (strpos($source, $relativeUrlRoot) !== 0)
	    		{
	    		  $source = $relativeUrlRoot.$source;
	    		}
    		}
    	}
      return sfContext::getInstance()->getController()->genUrl($source);
    }

    if(is_array($source))
    {
      if(isset($source[1]))
      {
	      if(is_object($source[1]))
	      {
		      return sfContext::getInstance()->getController()->genUrl(array(
		        'sf_route' => $source[0],
		        'sf_subject' => $source[1]
		      ));
	      }
    	}
    	else
    	{
        return sfContext::getInstance()->getController()->genUrl($source);
    	}
    }

    if(is_object($source))
    {
    	if ($module = dmModuleManager::getModuleOrNull($source))
    	{
	      return sfContext::getInstance()->getController()->genUrl(array(
	        'sf_route' => $module->getUnderscore().'_edit',
	        'sf_subject' => $source
	      ));
    	}
    }

    throw new dmException("Can not find href for $source");
  }

  protected function renderText()
  {
    if (empty($this->options['text']))
    {
    	if(is_object($this->options['source']))
    	{
	      if($this->options['source'] instanceof DmPage)
	      {
	        $text = $this->options['source']->get('name');
	      }
	      else
	      {
    	    $text = (string) $this->options['source'];
	      }
    	}
    	else
    	{
    		$text = $this->getBaseHref();
    	}
    }
    else
    {
    	$text = $this->options['text'];
    }

    return $text;
  }

}