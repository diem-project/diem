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
  
  public function getAdminListQuery(dmDoctrineQuery $q)
  {
    return $q->leftJoin($q->getRootAlias().'.Areas a')
    ->leftJoin('a.Zones z')
    ->leftJoin('z.Widgets w')
    ->orderBy('a.type DESC');
  }
}