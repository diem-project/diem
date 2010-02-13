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

  public function executeReloadAddMenu(dmWebRequest $request)
  {
    return $this->renderText($this->getService('front_add_menu')->build()->render());
  }

}