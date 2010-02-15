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

    if ($this->getUser()->can('page_add'))
    {
      $menu .= $this->getHelper()->link('+/dmPage/new')
      ->set('a.tipable.page_add_form.widget24.s24block.s24_page_add')
      ->text('')
      ->title($this->getI18n()->__('Add new page'));
    }

    if($this->getUser()->can('zone_add'))
    {
      $menu .= '<span class="zone_add move ui-draggable">Zone</span>';
    }

    $menu .= '<li class="dm_add_menu_actions clearfix">'.
    '<input class="dm_add_menu_search" title="'.$this->getI18n()->__('Search a widget').'" />'.
    '</li>';

    return $this->renderText($menu);
  }

}