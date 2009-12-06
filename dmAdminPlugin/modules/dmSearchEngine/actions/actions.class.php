<?php

class dmSearchEngineActions extends dmAdminBaseActions
{

  public function executeIndex(dmWebRequest $request)
  {
    $this->engine = $this->context->get('search_engine');
    
    $this->form = $this->getSearchForm();

    if ($this->query = trim($request->getParameter('query')))
    {
      $this->form->bind($request->getParameterHolder()->getAll());
      $this->pager = $this->getSearchPager($this->query);
    }
    else
    {
      $this->pager = null;
    }
  }

  public function executeReload()
  {
    $filesystem = $this->context->getFilesystem();
    
    if (!$filesystem->sf('dm:search-update'))
    {
      $this->getUser()->logError($this->context->getI18n()->__('Something went wrong when updating the search index'));
      
      if (sfConfig::get('sf_debug'))
      {
        $this->getUser()->logAlert(implode("\n", array(
          $filesystem->getLastExec('command'),
          $filesystem->getLastExec('output'),
          'return code : '.$filesystem->getLastExec('return')
        )));
        
        $dir = $this->context->get('search_engine')->getOption('dir');
        $this->getUser()->logError('Try running php symfony dm:search-update --env=dev in a terminal,'."\n".' and check permissions in '.$dir);
      }
    }
    else
    {
      $this->getUser()->logInfo($this->context->getI18n()->__('The search index has been updated'));
    }

    return $this->redirect('dmSearchEngine/index');
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
//      $pager->setUrlFormat($form->getUrlFormat());
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
    $form->setName('search');
    $form->setWidgets(array('query' => new sfWidgetFormInputText()));
    $form->setValidators(array('query' => new sfValidatorString()));

    return $form;
  }

}