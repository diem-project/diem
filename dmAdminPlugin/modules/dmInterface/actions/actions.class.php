<?php

include_once(dmOs::join(sfConfig::get('dm_core_dir'), 'modules/dmInterface/lib/BasedmInterfaceActions.php'));

class dmInterfaceActions extends BasedmInterfaceActions
{

  public function executeLoadPageTree(dmWebRequest $request)
  {
    return $this->renderAsync(array(
      'html'  => $this->getService('page_tree_view')->render(),
      'js'    => array('lib.jstree')
    ), true);
  }

}