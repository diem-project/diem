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
      /*
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
          $this->resource = preg_replace('|^page:\d+(.*)$|', 'app:front/'.$page->slug.'$1', $resource);
        }
        else
        {
          throw new dmException(sprintf('%s is not a valid link resource', $resource));
        }
      }
      
      if (strncmp($this->resource, 'app:', 4) === 0)
      {
        $type = 'uri';
        $app = substr($this->resource, 4);
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
      elseif ($this->resource{0} === '/')
      {
        $resource = $this->resource;
        
        /*
         * add relativeUrlRoot to absolute resource
         */
        if(($relativeUrlRoot = dmArray::get($this->serviceContainer->getParameter('request.context'), 'relative_url_root')) && (strpos($resource, $relativeUrlRoot) !== 0))
        {
          $resource = $relativeUrlRoot.$resource;
        }
      }
      elseif(strncmp($this->resource, '+/', 2) === 0)
      {
        $resource = substr($this->resource, 2);
      }
      else
      {
        $resource = $this->resource;
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
      if (($module = $this->resource->getDmModule()) && $module->hasAdmin())
      {
        $resource = array(
          'sf_route' => $module->getUnderscore(),
          'action'   => 'edit',
          'pk'       => $this->resource->getPrimaryKey()
        );
      }
      elseif($this->resource instanceof DmPage)
      {
        $resource = $this->serviceContainer->getService('script_name_resolver')->get('front').'/'.$this->resource->get('slug');
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
          $text = $this->resource->get('name');
        }
        else
        {
          $text = (string) $this->resource;
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