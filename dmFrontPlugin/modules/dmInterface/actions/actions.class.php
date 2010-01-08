<?php

include_once(dmOs::join(sfConfig::get('dm_core_dir'), 'modules/dmInterface/lib/BasedmInterfaceActions.php'));

class dmInterfaceActions extends BasedmInterfaceActions
{

  public function executeLoadPageTree(dmWebRequest $request)
  {
    $tree = new dmFrontRecursivePageList;

    $js =
      file_get_contents($this->context->get('helper')->getJavascriptFullPath('lib.tree-component')).
      file_get_contents($this->context->get('helper')->getJavascriptFullPath('lib.tree-css'))
    ;

    return $this->renderJson(array(
      'html' => $tree->render(),
      'js' => $js
    ));
  }

  public function executeReloadAddMenu(dmWebRequest $request)
  {
    return $this->renderText($this->getService('front_add_menu')->build()->render());
  }

}