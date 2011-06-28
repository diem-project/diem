<?php

class dmAdminModuleSpaceMenu extends dmMenu
{

  public function build(dmModuleSpace $space)
  {
    $this
    ->ulClass('dm_modules')
    ->children(array());

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

        $this->addChild($module->getName())
        ->liClass('dm_module')
        ->label(
          $this->helper->link('@'.$module->getUnderscore())
          ->text(
            $this->i18n->__($module->getPlural()).
            $this->helper->tag('span.infos', $nbRecordsText)
          )
          ->set('.dm_big_button')
        );
      }
    }

    return $this;
  }
}