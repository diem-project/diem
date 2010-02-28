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
    $menu = $this->getService('front_add_menu')->build()->render();

    if($this->getUser()->can('zone_add'))
    {
      $menu .= sprintf(
        '<span class="zone_add move ui-draggable">%s</span>',
        $this->getI18n()->__('Zone')
      );
    }

    $menu .= '<li class="dm_add_menu_actions clearfix">'.
    '<input class="dm_add_menu_search" title="'.$this->getI18n()->__('Search for a widget').'" />'.
    '</li>';

    return $this->renderText($menu);
  }

}