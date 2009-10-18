<?php
/**
 */
class PluginDmSettingTable extends myDoctrineTable
{
  
  public function fetchOneByName($name)
  {
    return $this->createQuery('r')
    ->where('r.name = ?', $name)
    ->fetchRecord();
  }
  
  public function getGroupNames()
  {
    $groups = dmDb::query('DmSetting s')->select('s.group_name')->groupBy('s.group_name')->fetchPDO();
    
    foreach($groups as $index => $group)
    {
      $groups[$index] = $group[0];
    }
    
    return $groups;
  }

  public function fetchGrouped()
  {
    $_records = $this->createQuery('s')->orderBy('s.group_name ASC')->withI18n()->fetchRecords();
    
    $currentGroupName = null;
    $records = array();
    foreach($_records as $_record)
    {
      $groupName = $_record->get('group_name');
      
      if ($groupName != $currentGroupName)
      {
        $records[$groupName] = array($_record);
        $currentGroupName = $groupName;
      }
      else
      {
        $records[$groupName][] = $_record;
      }
    }
    
    return $records;
  }
  
}