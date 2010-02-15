<?php

class BasedmPageActions extends dmAdminBaseActions
{

  public function executeIndex(dmWebRequest $request)
  {
    $this->redirect('dmPage/tree');
  }

  public function executeTree()
  {
    $this->tree = $this->getService('page_tree_view', 'dmAdminFullPageTreeView');
  }

  public function executeMetas()
  {
    $this->pages = dmDb::table('DmPage')->withI18n()->fetchRecords();

    $fields = array('name', 'title', 'description', '');
  }
}