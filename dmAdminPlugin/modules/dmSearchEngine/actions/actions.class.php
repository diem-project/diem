<?php

class dmSearchEngineActions extends dmAdminBaseActions
{

  public function executeIndex(dmWebRequest $request)
  {
    $this->engine = $this->getService('search_engine');
    
    $this->form = $this->getSearchForm();
    
    $params = $request->getParameter($this->form->getName());

    if ($this->query = trim($params['query']))
    {
      $this->form->bind(array('query' => $this->query));
      $this->pager = $this->getSearchPager($this->query);
    }
    else
    {
      $this->pager = null;
    }
    
    if ($this->getUser()->can('system'))
    {
      $this->shellUser = dmConfig::canSystemCall() ? exec('whoami') : 'www-data';
      
      $this->phpCli = dmConfig::canSystemCall() ? sfToolkit::getPhpCli() : '/path/to/php';
      
      $this->rootDir = sfConfig::get('sf_root_dir');
    }
  }

  public function executeReload()
  {
    try
    {
      $this->doReload();
      $this->getUser()->logInfo($this->getI18n()->__('The search index has been updated'));
    }
    catch(dmException $e)
    {
      $this->getUser()->logError($this->getI18n()->__('Something went wrong when updating the search index'));
      $this->getUser()->logAlert($e->getMessage());
      $this->getUser()->logError('Try running php symfony dm:search-update --env=dev in a terminal,'."\n".' and check permissions in '.$this->getService('search_engine')->getOption('dir'));
    }

    return $this->redirect('dmSearchEngine/index');
  }

  protected function doReload()
  {
    if(dmConfig::canSystemCall())
    {
      $filesystem = $this->getService('filesystem');
      
      if(!$filesystem->sf('dm:search-update'))
      {
        throw new dmException(implode("\n", array(
          $filesystem->getLastExec('command'),
          $filesystem->getLastExec('output'),
          'return code : '.$filesystem->getLastExec('return')
        )));
      }
    }
    else
    {
      $browser = $this->getService('web_browser');
      $url = $this->getHelper()->link('app:front/+/dmFront/reloadSearchIndex')->getHref();
      
      touch(sfConfig::get('sf_cache_dir').'/dm/search_index_'.time());
      $browser->get($url);

      if(200 != $browser->getResponseCode())
      {
        throw new dmException('The reload search index call returned the code '.$browser->getResponseCode());
      }
      
      $response = $browser->getResponseText();

      if('ok' != $response)
      {
        throw new dmException('An error occured: '.$response);
      }
    }
  }

  protected function getSearchPager($query)
  {
    $timeStart = microtime(true);

    $results = $this->engine->search($query);
    
    $this->time = sprintf("%01.2f", (microtime(true) - $timeStart));
    
    if (!empty($results))
    {
      $pager = new dmSearchPager($results, 20);
      $pager->setPage($this->getRequestParameter('page', 1));
      $pager->init();
    }
    else
    {
      $pager = null;
    }
    
    return $pager;
  }

  protected function getSearchForm()
  {
    $form = new dmForm();
    
    return $form
    ->setName('search')
    ->setWidgets(array('query' => new sfWidgetFormInputText()))
    ->setValidators(array('query' => new sfValidatorString()));
  }

}