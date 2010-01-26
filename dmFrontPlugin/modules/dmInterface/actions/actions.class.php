<?php

include_once(dmOs::join(sfConfig::get('dm_core_dir'), 'modules/dmInterface/lib/BasedmInterfaceActions.php'));

class dmInterfaceActions extends BasedmInterfaceActions
{

  public function executeLoadPageTree(dmWebRequest $request)
  {
    $tree = new dmFrontRecursivePageList();

    return $this->renderAsync(array(
      'html'  => $tree->render(),
      'js'    => array('lib.tree-component', 'lib.tree-css')
    ), true);
  }

  public function executeReloadAddMenu(dmWebRequest $request)
  {
    return $this->renderText($this->getService('front_add_menu')->build()->render());
  }

}