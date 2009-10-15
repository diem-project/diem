<?php
/**
 */
class PluginDmLayoutTable extends myDoctrineTable
{

  protected
  $firstLayout;

  public function findFirstOrCreate()
  {
    if (null === $this->firstLayout)
    {
      if (!$this->firstLayout = $this->createQuery()->dmCache()->fetchRecord())
      {
        $this->firstLayout = dmDb::create('DmLayout', array('name' => 'Global'))->saveGet();
      }
    }

    return $this->firstLayout;
  }
}