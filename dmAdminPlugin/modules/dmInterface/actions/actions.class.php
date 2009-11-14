<?php

include_once(dmOs::join(sfConfig::get('dm_core_dir'), 'modules/dmInterface/lib/BasedmInterfaceActions.php'));

class dmInterfaceActions extends BasedmInterfaceActions
{

  public function executeLoadPageTree(sfWebRequest $request)
  {
    $tree = new dmAdminRecursivePageList();

    $js =
      file_get_contents($this->context->get('helper')->getJavascriptFullPath('lib.tree-component')).
      file_get_contents($this->context->get('helper')->getJavascriptFullPath('lib.tree-css'))
    ;

    return $this->renderJson(array(
      'html' => $tree->render(),
      'js' => $js
    ));
  }

}