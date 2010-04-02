<?php

class dmWidgetListView extends dmWidgetProjectModelView
{

  public function configure()
  {
    parent::configure();

    $this->addRequiredVar(array('orderField', 'orderType'));

    foreach($this->dmComponent->getOption('filters', array()) as $filter)
    {
      if ($filterModule = $this->dmModule->getAncestor($filter))
      {
        if (!$this->allowFilterAutoRecordId($filterModule))
        {
          $this->addRequiredVar($filter.'Filter');
        }
      }
    }
    
    $this->isIndexable = !$this->dmModule->hasPage();
  }

  /*
   * Will put filters in an array
   * @return array viewVars
   */
  protected function filterViewVars(array $vars = array())
  {
    $viewVars = parent::filterViewVars($vars);
    
    $viewVars['maxPerPage'] = isset($viewVars['maxPerPage']) ? $viewVars['maxPerPage'] : 0;

    $filters = array();
    foreach($viewVars as $key => $val)
    {
      if ('Filter' === substr($key, -6))
      {
        $filters[substr($key, 0, strlen($key)-6)] = $val;
        unset($viewVars[$key]);
      }
    }

    $viewVars['filters'] = $filters;

    $viewVars['page'] = $this->getService('request')->getParameter('page', 1);

    return $viewVars;
  }

  protected function allowFilterAutoRecordId(dmModule $filterModule)
  {
    return $this->context->getPage() ? $this->context->getPage()->getDmModule()->knows($filterModule) : false;
  }
}