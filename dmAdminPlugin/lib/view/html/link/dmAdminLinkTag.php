<?php

class dmAdminLinkTag extends dmBaseLinkTag
{
  protected
  $serviceContainer;
  
  public function __construct($resource, dmAdminBaseServiceContainer $serviceContainer, array $options)
  {
    $this->resource         = empty($resource) ? '@homepage' : $resource;
    $this->serviceContainer = $serviceContainer;
    
    $this->initialize($options);
  }
  
  protected function initialize(array $options = array())
  {
    parent::initialize($options);

    $this->addAttributeToRemove(array('external_blank'));
    
    if($this->options['external_blank'] && is_string($this->resource) && strpos($this->resource, '://'))
    {
      try
      {
        $absoluteUrlRoot = dmArray::get($this->serviceContainer->getParameter('request.context'), 'absolute_url_root');

        if(0 !== strncmp($this->resource, $absoluteUrlRoot, strlen($absoluteUrlRoot)))
        {
          $this->target('_blank');
        }
      }
      catch(Exception $e)
      {
        // do nothing, exception will be thrown when rendering the link
      }
    }
  }
  
  public function render()
  {
    try
    {
      return parent::render();
    }
    catch(Exception $e)
    {
      return '<a class="link">'.$e->getMessage().'</a>';
    }
  }

  protected function getBaseHref()
  {
    if(is_string($this->resource))
    {
      $resource = $this->resource;
      
      /**
       * If a blank space is found in the source,
       * remove characters after it
       * because they are just a comment
       * ex : page:1?var=val Home
       */
      if ($blankSpacePos = strpos($resource, ' '))
      {
        $resource = substr($resource, 0, $blankSpacePos);
      }
      
      if (strncmp($resource, 'page:', 5) === 0)
      {
        $pageResource = preg_replace('|^(page:\d+).*$|', '$1', $resource);
        
        if ($page = dmDb::table('DmPage')->findOneBySource($pageResource))
        {
          $resource = preg_replace('|^page:\d+(.*)$|', 'app:front/'.$page->get('slug').'$1', $resource);
        }
        else
        {
          throw new dmException(sprintf('%s is not a valid link resource', $resource));
        }
      }
      elseif(strncmp($resource, 'media:', 6) === 0)
      {
        $mediaResource = preg_replace('|^media:(\d+).*|', '$1', $resource);
        
        if ($media = dmDb::table('DmMedia')->findOneByIdWithFolder($mediaResource))
        {
          $resource = '/'.$media->getWebPath();
          /*
           * add relativeUrlRoot to absolute resource
           */
          if($relativeUrlRoot = dmArray::get($this->serviceContainer->getParameter('request.context'), 'relative_url_root'))
          {
            $resource = $relativeUrlRoot.$resource;
          }
        }
        else
        {
          throw new dmException(sprintf('%s is not a valid media resource. The media with id %s does not exist', $resource, $mediaResource));
        }
      }

      if (strncmp($resource, 'app:', 4) === 0)
      {
        $type = 'uri';
        $app = substr($resource, 4);
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
        
        $resource = $this->serviceContainer->getService('script_name_resolver')->get($app).$slug;
      }
      elseif ($resource{0} === '/')
      {
        /*
         * add relativeUrlRoot to absolute resource
         */
        if(($relativeUrlRoot = dmArray::get($this->serviceContainer->getParameter('request.context'), 'relative_url_root')) && (strpos($resource, $relativeUrlRoot) !== 0))
        {
          $resource = $relativeUrlRoot.$resource;
        }
      }
      elseif(strncmp($resource, '+/', 2) === 0)
      {
        $resource = substr($resource, 2);
      }
    }
    elseif(is_array($this->resource))
    {
      if(isset($this->resource[1]) && is_object($this->resource[1]))
      {
        $resource =array(
          'sf_route' => $this->resource[0],
          'sf_subject' => $this->resource[1]
        );
      }
      else
      {
        $resource = $this->resource;
      }
    }

    elseif(is_object($this->resource) && $this->resource instanceof dmDoctrineRecord)
    {
      if($this->resource instanceof DmPage)
      {
      	//@todo add check in $resource generation for /:sf_culture/:slug in route
        $resource = $this->serviceContainer->getService('script_name_resolver')->get('front').'/'.$this->resource->get('slug');
      }
      elseif (($module = $this->resource->getDmModule()) && $module->hasAdmin() && $module->getSecurityManager()->userHasCredentials('edit', $this->resource))
      {
        $resource = array(
          'sf_route' => $module->getUnderscore(),
          'action'   => 'edit',
          'pk'       => $this->resource->getPrimaryKey()
        );
      }else{
      	$resource = '#';
      }
    }
    
    if(isset($resource))
    {
      if (is_string($resource) && (strncmp($resource, '#', 1) === 0 || strncmp($resource, 'mailto:', 7)  === 0))
      {
        return $resource;
      }
      else
      {
        return $this->serviceContainer->getService('controller')->genUrl($resource);
      }
    }

    throw new dmException('Can not find href for '. $this->resource);
  }

  protected function renderText()
  {
    if (!isset($this->options['text']))
    {
      if(is_object($this->resource))
      {
        if($this->resource instanceof DmPage)
        {
          $text = $this->resource->_getI18n('name');
        }
        else
        {
          $text = (string) $this->resource;
        }

        $text = dmString::escape($text);
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

  public function getAbsoluteHref()
  {
    $href = $this->getHref();

    if(strpos($href, '://'))
    {
      return $href;
    }

    return $this->serviceContainer->getService('request')->getUriPrefix().$href;
  }

}
