<?php

class dmAdminGeneratorActions extends dmAdminBaseActions
{

  public function executeSaveSort(sfWebRequest $request)
  {
    $this->forward404Unless(
      $module = $this->context->getModuleManager()->getModuleOrNull(
        $request->getParameter('dm_module')
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
      $module->getTable()->doSort($elements);
    }
    catch(Exception $e)
    {
      if ($this->getUser()->can('system'))
      {
        throw $e;
      }
      $this->getUser()->logError($this->context->getI18n()->__('A problem occured when sorting the items'), true);
    }

    $this->getUser()->logInfo($this->context->getI18n()->__('The items have been sorted successfully'), true);

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

  // changing max_per_page
  public function executeChangeMaxPerPage(sfWebRequest $request)
  {
    $this->forward404Unless(
      $module = $this->context->getModuleManager()->getModuleOrNull(
        $request->getParameter('dm_module')
      )
    );

    if ($maxPerPage = $request->getParameter('max_per_page'))
    {
      $maxPerPages = sfConfig::get('dm_admin_max_per_page', array(10));
      $maxPerPage = in_array($maxPerPage, $maxPerPages) ? $maxPerPage : dmArray::first($maxPerPages);
      $this->getUser()->setAttribute($module.'.max_per_page', $maxPerPage, 'admin_module');
    }

    return $this->redirect('@'.$module->getUnderscore());
  }

}