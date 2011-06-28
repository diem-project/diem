<?php

class dmAdminGeneratorActions extends dmAdminBaseActions
{
  
  public function executeRevert(dmWebRequest $request)
  {
    $model    = $request->getParameter('model');
    $pk       = $request->getParameter('pk');
    $version  = $request->getParameter('version');
    
    $this->forward404Unless($record = dmDb::table($model)->find($pk));
    
    $record->revert($version);
    $record->save();
    
    $this->getUser()->logInfo($this->getI18n()->__('%1% has been reverted to version %2%', array(
      '%1%' => $record->__toString(),
      '%2%' => $version
    )));
    
    return $this->redirectBack();
  }

  public function executeSaveSort(sfWebRequest $request)
  {
    $this->forward404Unless(
      $module = $this->context->getModuleManager()->getModuleOrNull($request->getParameter('dm_module'))
    );

    $elements = $request->getParameter('dm_sort_element');

    $currentPosition = 1;
    foreach($elements as $elementId => $position)
    {
      $elements[$elementId] = $currentPosition++;
    }

    try
    {
      $module->getTable()->doSort($elements);
    }
    catch(Exception $e)
    {
      if ($this->getUser()->can('system'))
      {
        throw $e;
      }
      $this->getUser()->logError($this->getI18n()->__('A problem occured when sorting the items'), true);
    }

    $this->getUser()->logInfo($this->getI18n()->__('The items have been sorted successfully'), true);

    return $this->redirectBack();
  }

  public function executeSaveSortReferers(sfWebRequest $request)
  {
    $this->forward404Unless(
      $module = $this->context->getModuleManager()->getModuleOrNull(
        $request->getParameter('dm_module')
      )
    );

    $this->forward404Unless(
      $refererModule = $this->context->getModuleManager()->getModuleOrNull(
        $request->getParameter('dm_referer_module')
      )
    );

    $elements = $request->getParameter('dm_sort_element');

    $currentPosition = 1;
    foreach($elements as $elementId => $position)
    {
      $elements[$elementId] = $currentPosition++;
    }

    try
    {
      $refererModule->getTable()->doSort($elements);
    }
    catch(Exception $e)
    {
      if ($this->getUser()->can('system'))
      {
        throw $e;
      }
      $this->getUser()->logError(dm::getI18n()->__('A problem occured when sorting the items'), array(), true);
    }

    $this->getUser()->logInfo(dm::getI18n()->__('The items have been sorted successfully'), array(), true);

    return $this->redirectBack();
  }

  public function executeChangeMaxPerPage(sfWebRequest $request)
  {
    $this->forward404Unless(
      $module = $this->context->getModuleManager()->getModuleBySfName(
        $sfModule = $request->getParameter('dm_module')
      )
    );

    if ($maxPerPage = $request->getParameter('max_per_page'))
    {
      $maxPerPages = sfConfig::get('dm_admin_max_per_page', array(10));
      $maxPerPage = in_array($maxPerPage, $maxPerPages) ? $maxPerPage : dmArray::first($maxPerPages);
      $this->getUser()->setAttribute($sfModule.'.max_per_page', $maxPerPage, 'admin_module');
      $this->getUser()->setAttribute($sfModule.'.page', 1, 'admin_module');
    }

    return $this->redirectBack();
  }

  public function executeShowMoreRelatedRecords(sfWebRequest $request)
  {
    $this->forward404Unless(
      $record = dmDb::table($request->getParameter('model'))->find($request->getParameter('pk'))
    );

    $view = $this->getServiceContainer()->mergeParameter('related_records_view.options', array(
      'record'  => $record,
      'alias'   => $request->getParameter('alias'),
      'max'     => 999
    ))->getService('related_records_view');

    return $this->renderText($view->renderList());
  }

  public function executeMove(dmWebRequest $request)
  {
    $this->forward404Unless(
      $module = $this->context->getModuleManager()->getModuleOrNull(
        $request->getParameter('dm_module')
      )
    );

    $this->forward404Unless(
      $module instanceof dmProjectModule && $module->getTable() instanceof dmDoctrineTable && $module->getTable()->isNestedSet()
    );

    $this->forward404Unless(
      $model = $module->getTable()->find($request->getParameter('model'))
    );

    if($nextToModel = $module->getTable()->find($request->getParameter('previous')))
    {
      $model->Node->moveAsNextSiblingOf($nextToModel);
    }
    elseif($inModel = $module->getTable()->find($request->getParameter('to')))
    {
      $model->Node->moveAsFirstChildOf($inModel);
    }
    else
    {
      $this->forward404('Bad operation');
    }

    return $this->renderText('ok');
  }
}