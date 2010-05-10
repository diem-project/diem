<?php
/**
 */
class PluginDmPageViewTable extends myDoctrineTable
{

  public function findOneByName($name)
  {
    return $this->createQuery('p')->where('p.name = ?', $name)->fetchRecord();
  }

  public function findOneByModuleAndAction($module, $action)
  {
    return $this->findOneByName($this->getNameForModuleAndAction($module, $action));
  }

  /**
   * @return DmPageView created record
   */
  public function createFromModuleAndAction($module, $action)
  {
    return $this->create(array(
      'name' => $this->getNameForModuleAndAction($module, $action)
    ));
  }

  public function getNameForModuleAndAction($module, $action)
  {
    return sprintf('%s.%s', $module, $action);
  }

}