<?php

class BasedmPageActions extends dmAdminBaseActions
{

  public function executeIndex(dmWebRequest $request)
  {

  }

  public function executeTree()
  {
    $this->tree = $this->getService('page_tree_view', 'dmAdminFullPageTreeView');
  }
}