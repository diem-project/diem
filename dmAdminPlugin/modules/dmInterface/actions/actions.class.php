<?php

include_once(dmOs::join(sfConfig::get('dm_core_dir'), 'modules/dmInterface/lib/BasedmInterfaceActions.php'));

class dmInterfaceActions extends BasedmInterfaceActions
{

  public function executeLoadPageTree(dmWebRequest $request)
  {
    $tree = new dmAdminRecursivePageList();

    return $this->renderAsync(array(
      'html'  => $tree->render(),
      'js'    => array('lib.tree-component', 'lib.tree-css')
    ));
  }

}