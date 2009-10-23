<?php

class dmPageNotFoundHandler
{
  protected
  $dispatcher;

  public function __construct(dmFrontServiceContainer $serviceContainer, sfEventDispatcher $dispatcher)
  {
    $this->serviceContainer = $serviceContainer;
    $this->dispatcher       = $dispatcher;
  }

  public function getRedirection($slug)
  {
    if ($redirection = $this->validate($this->notifyBefore($slug)))
    {
      return $redirection;
    }

    if ($redirection = $this->validate($this->useDmRedirect($slug)))
    {
      return $redirection;
    }

    if ($redirection = $this->validate($this->useSearchIndex($slug)))
    {
      return $redirection;
    }

    if ($redirection = $this->validate($this->notifyAfter($slug)))
    {
      return $redirection;
    }

    return false;
  }
  
  protected function validate($redirection)
  {
    if (!$redirection)
    {
      return false;
    }
    
    try
    {
      return $this->serviceContainer->get('controller')->genUrl($redirection);
    }
    catch(Exception $e)
    {
      $this->dispatcher->notify(new sfEvent($this, 'application.log', array(
        'Can not redirect to '.$redirection,
        sfLogger::ERR
      )));
      
      if (sfConfig::get('dm_debug'))
      {
        throw $e;
      }
    }
  }

  protected function notifyBefore($slug)
  {
    $event = new sfEvent($this, 'dm.page_not_found.before', array('slug' => $slug));

    $this->dispatcher->notifyUntil($event);

    if ($event->isProcessed())
    {
      return $event->getReturnValue();
    }
  }

  protected function useDmRedirect($slug)
  {
    if ($dmRedirect = dmDb::query('DmRedirect r')->where('r.source = ?', $slug)->fetchRecord())
    {
      if ($page = dmDb::table('DmPage')->findOneBySource($dmRedirect->dest))
      {
        $redirectionUrl = dmFrontLinkTag::build($page)->getHref();
      }
      else
      {
        $redirectionUrl = $dmRedirect->dest;
      }

      return $redirectionUrl;
    }
  }

  protected function useSearchIndex($slug)
  {
    if (!dmConfig::get('smart_404'))
    {
      return false;
    }
    
    try
    {
      $searchIndex = $this->serviceContainer->get('search_engine')->getCurrentIndex();

      $query = Zend_Search_Lucene_Search_QueryParser::parse(
      str_replace('/', ' ', dmString::unSlugify($slug))
      );

      $results = $searchIndex->search($query);

      $foundPage = null;
      foreach($results as $result)
      {
        if ($result->getScore() > 0.5)
        {
          if($foundPage = $result->getPage())
          {
            break;
          }
        }
        else
        {
          break;
        }
      }

      if ($foundPage)
      {
        return dmFrontLinkTag::build($foundPage)->getHref();
      }
    }
    catch(Exception $e)
    {
      $this->dispatcher->notify(new sfEvent($this, 'application.log', array(
        'Can not use search index to find redirection for slug '.$slug,
        sfLogger::ERR
      )));
      
      if(sfConfig::get('dm_debug'))
      {
        throw $e;
      }
    }
  }

  protected function notifyAfter($slug)
  {
    $event = new sfEvent($this, 'dm.page_not_found.after', array('slug' => $slug));

    $this->dispatcher->notifyUntil($event);

    if ($event->isProcessed())
    {
      return $event->getReturnValue();
    }
  }
}