<?php

class BasedmCoreActions extends dmBaseActions
{

  public function executePing(dmWebRequest $request)
  {
    $recordId = $this->request->getParameter('record_id', 0);

    $data = array(
      'user_id'   => $this->getUser()->getUserId(),
      'time'      => $_SERVER['REQUEST_TIME'],
      'app'       => sfConfig::get('sf_app'),
      'module'    => $this->request->getParameter('sf_module'),
      'action'    => $this->request->getParameter('sf_action'),
      'record_id' => $recordId,
      'culture'   => $this->getUser()->getCulture()
    );

    dmDb::table('DmLock')->ping($data);

    $users = dmDb::table('DmLock')->getUserNames();

    if($recordId && count($users) > 1 && count($locks = dmDb::table('DmLock')->getLocks($data)))
    {
      foreach($locks as $index => $lock)
      {
        $locks[$index] = $this->getI18n()->__('%user% is browsing this page, you should not modify it now.', array(
          '%user%' => '<strong>'.$lock.'</strong>'
        ));
      }
    }
    else
    {
      $locks = array();
    }

    return $this->renderJson(array(
      'users' => implode('|', $users),
      'locks' => implode('|', $locks)
    ));
  }

  public function executeThumbnail(dmWebRequest $request)
  {
    $tag = $this->getHelper()->media($request->getParameter('source'));

    foreach(array('width', 'height', 'method', 'quality') as $key)
    {
      if ($request->hasParameter($key))
      {
        $tag->set($key, $request->getParameter($key));
      }
    }

    return $this->renderText($tag->render());
  }

  public function executeSelectCulture(dmWebRequest $request)
  {
    $this->forward404Unless(
      $culture = $request->getParameter('culture'),
      'No culture specified'
    );

    $this->forward404Unless(
      $this->getI18n()->cultureExists($culture),
      sprintf('The %s culture does not exist', $culture)
    );

    $this->getUser()->setCulture($culture);

    return $this->redirectBack();
  }

  public function executeRefresh(dmWebRequest $request)
  {
    $this->next = array(
      'type' => 'ajax',
      'url'  => $this->getHelper()->link('+/dmCore/refreshStep?step=1')->getHref(),
      'msg'  => $this->getI18n()->__('Code generation')
    );

    $this->setLayout(false);

    $this->getUser()->setAttribute('dm_refresh_back_url', $this->getBackUrl());
  }

  public function executeRefreshStep(dmWebRequest $request)
  {
    if ($request->hasParameter('dm_use_thread'))
    {
      $this->context->getServiceContainer()
      ->mergeParameter('page_tree_watcher.options', array('use_thread' => $request->getParameter('use_thread')))
      ->reload('page_tree_watcher');
    }

    $this->step = $request->getParameter('step');

    try
    {
      switch($this->step)
      {
        case 1:
          @$this->getService('cache_manager')->clearAll();

          if ($this->getUser()->can('system'))
          {
            @$this->getService('filesystem')->sf('dmFront:generate');

            @dmFileCache::clearAll();
          }

          $data = array(
            'msg'  => $this->getI18n()->__('Page synchronization'),
            'type' => 'ajax',
            'url'  => $this->getHelper()->link('+/dmCore/refreshStep')->param('step', 2)->getHref()
          );
          break;

        case 2:
          $this->getService('page_tree_watcher')->synchronizePages();

          $data = array(
            'msg'  => $this->getI18n()->__('SEO synchronization'),
            'type' => 'ajax',
            'url'  => $this->getHelper()->link('+/dmCore/refreshStep')->param('step', 3)->getHref()
          );
          break;

        case 3:
          $this->getService('page_tree_watcher')->synchronizeSeo();

          if (count($this->getI18n()->getCultures()) > 1)
          {
            $this->getService('page_i18n_builder')->createAllPagesTranslations();
          }

          $data = array(
            'msg'  => $this->getI18n()->__('Interface regeneration'),
            'type' => 'redirect',
            'url'  => $this->getUser()->getAttribute('dm_refresh_back_url')
          );

          $this->context->getEventDispatcher()->notify(new sfEvent($this, 'dm.refresh', array()));
          $this->getUser()->getAttributeHolder()->remove('dm_refresh_back_url');
          $this->getUser()->logInfo('Project successfully updated');
          break;
      }
    }
    catch(Exception $e)
    {
      $this->getUser()->logError($this->getI18n()->__('Something went wrong when updating project'));

      $data = array(
        'msg'  => $this->getI18n()->__('Something went wrong when updating project'),
        'type' => 'redirect',
        'url'  => $this->getUser()->getAttribute('dm_refresh_back_url')
      );

      if (sfConfig::get('sf_debug'))
      {
        if ($request->isXmlHttpRequest())
        {
          $data['url'] = str_replace('dm_xhr=1', 'dm_xhr=0', $request->getUri().'&dm_use_thread=0');
        }
        else
        {
          throw $e;
        }
      }
    }

    return $this->renderJson($data);
  }

  public function executeMarkdown(dmWebRequest $request)
  {
    return $this->renderText($this->getService('markdown')->toHtml($request->getParameter('text')));
  }

}