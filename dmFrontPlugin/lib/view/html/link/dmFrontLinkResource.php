<?php

class dmFrontLinkResource
{
  protected
    $source,
    $subject,
    $type,
    $params;
    
  public function getType()
  {
    return $this->type;
  }
  
  public function getSubject()
  {
    return $this->subject;
  }
  
  public function setSubject($subject)
  {
    $this->subject = $subject;
  }
  
  public function getParams()
  {
    return $this->params;
  }
  
  public function hasParams()
  {
    return !empty($this->params);
  }
  
  public function initialize($source)
  {
    $this->source = $source;
    $this->params = false;
    
    if (empty($source))
    {
      $this->type = 'page';
      $this->subject = dmDb::table('DmPage')->findOneBySource(null);
    }
    elseif (is_string($source))
    {
      /*
       * If a blank space is found in the source,
       * remove characters after it
       * because they are just a comment
       * i.e. page:1?var=val Home
       */
      if ($blankSpacePos = strpos($source, ' '))
      {
        $source = substr($source, 0, $blankSpacePos);
      }
      
      /*
       * Extract url parameters from source string
       */
      $this->params = dmString::getDataFromUrl($source);
      $source = dmString::getBaseFromUrl($source);

      if ('@homepage' === $source)
      {
        $source = 'main/root';
      }
      
      if (strncmp($source, 'page:', 5) === 0)
      {
        if ($page = dmDb::table('DmPage')->findOneBySource($source))
        {
          $this->type = 'page';
          $this->subject = $page;
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
          $this->type = 'media';
          $this->subject = $media;
        }
        else
        {
          throw new dmException(sprintf('%s is not a valid link resource', $source));
        }
      }
      elseif (strncmp($source, 'app:', 4) === 0)
      {
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
        
        $this->type = 'uri';
        $this->subject = dmContext::hasInstance() ? dmContext::getInstance()->get('script_name_resolver')->get($app).$slug : $slug;
      }
      elseif (strncmp($source, '@', 1) === 0)
      {
        $this->type = 'route';
        $this->subject = $source;
      }
      elseif(
          strncmp($source, 'http://', 7)  === 0
      ||  strncmp($source, 'https://', 7)  === 0
      ||  strncmp($source, 'ftp://', 6)   === 0
      ||  strncmp($source, 'mailto:', 7)  === 0
      ||  strncmp($source, '#', 1)        === 0
      )
      {
        $this->type = 'uri';
        $this->subject = $source;
      }
      elseif(strncmp($source, '/', 1) === 0)
      {
        $this->type = 'uri';
        $this->subject = $source;
      }
      elseif(strncmp($source, '+/', 2) === 0)
      {
        $this->type = 'action';
        $this->subject = substr($source, 2);
      }
      elseif(substr_count($source, '/') === 1)
      {
        if ($page = dmDb::table('DmPage')->findOneBySource($source))
        {
          $this->type = 'page';
          $this->subject = $page;
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
        $this->type = 'page';
        $this->subject = $source;
      }
      elseif ($source instanceof DmMedia)
      {
        $this->type = 'media';
        $this->subject = $source;
      }
      elseif($source instanceof dmDoctrineRecord)
      {
        if ($module = $source->getDmModule())
        {
          if($module->hasPage())
          {
            $this->type = 'record';
            $this->subject = $source;
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
      elseif ($source instanceof Exception)
      {
        $this->type = 'error';
        $this->subject = $source;
      }
    }
    elseif(is_array($source))
    {
      if(isset($source[1]))
      {
        if(is_object($source[1]))
        {
          $this->type = 'action';
          $this->subject = array(
            'sf_route' => $source[0],
            'sf_subject' => $source[1]
          );
        }
      }
      else
      {
        $this->type = 'action';
        $this->subject = $source;
      }
    }

    if(empty($this->type) || empty($this->subject))
    {
      throw new dmException(sprintf('dmFrontLinkResource can not determine type of %s', $source));
    }
    
    return $this;
  }
}