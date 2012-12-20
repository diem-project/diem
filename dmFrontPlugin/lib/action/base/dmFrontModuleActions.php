<?php

class dmFrontModuleActions extends myFrontBaseActions
{
  protected
  $dmModule;
  
  /**
   * @return dmProjectModule the current module for this action
   */
  protected function getDmModule()
  {
    if (null === $this->dmModule)
    {
      $this->dmModule = $this->context->getModuleManager()->getModule(preg_replace('|^(.+)Actions$|', '$1', get_class($this)));
    }

    return $this->dmModule;
  }

  /**
   * @return myDoctrineTable the table of the current module for this action
   */
  protected function getTable()
  {
    return $this->getDmModule()->getTable();
  }
}