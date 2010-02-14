<?php

class BasedmPageActions extends dmAdminBaseActions
{

  public function executeIndex(dmWebRequest $request)
  {
    $this->getUser()->logAlert('This feature is not available yet');
    return;

    $this->pages = dmDb::table('DmPage')->withI18n()->fetchRecords();

    $fields = array('name', 'title', 'description', '');

  }

  public function executeTree()
  {
    $this->tree = $this->getService('page_tree_view', 'dmAdminFullPageTreeView');
  }
}