<?php

class dmWidgetListView extends dmWidgetProjectModelView
{

  public function configure()
  {
    parent::configure();

    $this->addRequiredVar(array('maxPerPage', 'orderField', 'orderType'));

    foreach($this->dmAction->getParam('filters', array()) as $filter)
    {
      if ($filterModule = $this->dmModule->getAncestor($filter))
      {
        if (!$this->allowFilterAutoRecordId($filterModule))
        {
          $this->addRequiredVar($filter);
        }
      }
    }
  }

  /*
   * Will put filters in an array
   * @return array viewVars
   */
  public function getViewVars(array $vars = array())
  {
    $viewVars = parent::getViewVars($vars);

    $filters = array();
    foreach($viewVars as $key => $val)
    {
      if (strncmp($key, 'filter', 6) === 0)
      {
        $filters[dmString::modulize(preg_replace('|^filter(.+)$|', '$1', $key))] = $val;
        unset($viewVars[$key]);
      }
    }

    $viewVars['filters'] = $filters;

    return $viewVars;
  }

  protected function allowFilterAutoRecordId(dmModule $filterModule)
  {
    return dmContext::getInstance()->getPage()->getDmModule()->knows($filterModule);
  }
}