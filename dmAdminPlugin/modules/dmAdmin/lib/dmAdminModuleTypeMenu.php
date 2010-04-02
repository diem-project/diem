<?php

class dmAdminModuleTypeMenu extends dmMenu
{

  public function build(dmModuleType $type)
  {
    $this
    ->ulClass('dm_module_spaces dm_module_type mt10')
    ->children(array());

    foreach($type->getSpaces() as $space)
    {
      $spaceMenu = $this->addChild($space->getPublicName())
      ->ulClass('dm_modules dm_box_inner pl10 pr10')
      ->liClass('dm_module_space dm_module_type_show dm_box fleft mr20 mb20')
      ->label(
        $this->helper->tag('h2.title',
          $this->helper->link($this->serviceContainer->getService('routing')->getModuleSpaceUrl($space))
          ->text($this->i18n->__($space->getPublicName()))
          ->set('.center')
        )
      );

      foreach($space->getModules() as $key => $module)
      {
        if ($this->user->canAccessToModule($module))
        {
          if($nbRecords = $module->hasModel() ? $module->getTable()->count() : null)
          {
            $choice = new sfChoiceFormat();

            $nbRecordsText = $choice->format(
              $this->i18n->__('[0]no element|[1]1 element|(1,+Inf]%1% elements', array('%1%' => $nbRecords)),
              $nbRecords
            );
          }
          else
          {
            $nbRecordsText = '';
          }

          $spaceMenu->addChild($module->getName())
          ->liClass('dm_module')
          ->label(
            $this->helper->link('@'.$module->getUnderscore())->text($this->i18n->__($module->getPlural())).
            $this->helper->tag('p.infos', $nbRecordsText)
          );
        }
      }

      if(!$spaceMenu->hasChildren())
      {
        $this->removeChild($spaceMenu);
      }
    }

    return $this;
  }
}
