<?php

abstract class dmFrontLinkTag extends dmLinkTag
{

  public static function build($source = null)
  {
    $params = false;
    
    if (is_null($source))
    {
    	$type = 'page';
      $source = dmDb::table('DmPage')->findOneBySource($source);
    }
    elseif (is_string($source))
    {
    	/*
    	 * If a blank space is found in the source,
    	 * remove characters after it
    	 * because they are just a comment
    	 * ex : page:1?var=val Home
    	 */
    	if ($blakSpacePos = strpos($source, ' '))
    	{
    		$source = substr($source, 0, $blakSpacePos);
    	}
    	/*
    	 * Extract url parameters from source string
    	 */
    	$params = self::getDataFromUrl($source);
    	$source = self::getBaseFromUrl($source);
    	
      if (strncmp($source, 'page:', 5) === 0)
      {
        if ($page = dmDb::table('DmPage')->findOneBySource($source))
        {
          $type = 'page';
          $source = $page;
        }
        else
        {
          throw new dmException(sprintf('%s is not a valid link resource', $source));
        }
      }
      elseif (strncmp($source, 'media:', 6) === 0)
      {
        if ($media = dmDb::table('DmMedia')->findOneByIdWithFolder(substr($source, 6)))
        {
          $type = 'media';
          $source = $media;
        }
        else
        {
          throw new dmException(sprintf('%s is not a valid link resource', $source));
        }
      }
      elseif (strncmp($source, 'app:', 4) === 0)
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
      elseif(
          strncmp($source, "http://", 7)  === 0
      ||  strncmp($source, "ftp://", 6)   === 0
      ||  strncmp($source, "mailto:", 7)  === 0
      ||  strncmp($source, "@", 1)        === 0
      ||  strncmp($source, "#", 1)        === 0
      )
      {
        $type = 'uri';
      }
      elseif(strncmp($source, "+/", 2) === 0)
      {
        $type = 'action';
        $source = substr($source, 2);
      }
      elseif(substr_count($source, '/') === 1)
      {
      	if ($page = dmDb::table('DmPage')->findOneBySource($source))
      	{
      		$type = 'page';
      		$source = $page;
      	}
      	else
      	{
      		throw new dmException(sprintf('%s is not a valid link resource', $source));
      	}
      }
      else
      {
        throw new dmException(sprintf('%s is not a valid link resource', $source));
      }
    }
    elseif(is_object($source))
    {
      if ($source instanceof DmPage)
      {
        $type = 'page';
      }
      elseif ($source instanceof DmMedia)
      {
        $type = 'media';
      }
      elseif($source instanceof myDoctrineRecord)
      {
      	if ($module = $source->getDmModule())
	      {
	      	if($module->hasPage())
	      	{
	      		$type = 'record';
	      	}
	        else
	        {
	          throw new dmException(sprintf('%s module has no page', $module));
	        }
	      }
	      else
	      {
	      	throw new dmException(sprintf('%s object can not be associated to a page', get_class($source)));
	      }
      }
    }
    elseif(is_array($source))
    {
      if(isset($source[1]))
      {
        if(is_object($source[1]))
        {
          $source = array(
            'sf_route' => $source[0],
            'sf_subject' => $source[1]
          );
          $type = 'action';
        }
      }
      else
      {
        return sfContext::getInstance()->getController()->genUrl($source);
      }
    }

    if(empty($type))
    {
    	throw new dmException(sprintf('dmFrontLinkTag can not determine type of %s', $source));
    }

    $linkClass = 'dmFrontLinkTag'.dmString::camelize($type);

    try
    {
      $linkTagObject = new $linkClass($source);
      
      if($params)
      {
        $linkTagObject->params($params);
      }
    }
    catch(Exception $e)
    {
    	if (sfConfig::get('dm_debug'))
    	{
    		throw $e;
    	}
    	else
    	{
    		$linkTagObject = new dmFrontLinkTagError($e);
    	}
    }

    return $linkTagObject;
  }

  public function __construct($source)
  {
    $this->set('source', $source)->addClass('link');

    $this->configure();
  }
  
  protected function renderText()
  {
    if (isset($this['text']))
    {
      return $this['text'];
    }

    return $this->getBaseHref();
  }
}