<?php

/**
 * Install Diem
 */
class dmUpgradeTask extends dmContextTask
{
  protected
  $diemVersions = array(
    '500ALPHA4'
  );
  
  /**
   * @see sfTask
   */
  protected function configure()
  {
    parent::configure();
    
    $this->namespace = 'dm';
    $this->name = 'upgrade';
    $this->briefDescription = 'Safely upgrade a project to the current Diem version. Can be run several times without side effect.';

    $this->detailedDescription = $this->briefDescription;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $this->withDatabase();
    
    $this->logSection('diem', 'Upgrade '.dmProject::getKey());
    
    foreach($this->diemVersions as $version)
    {
      $upgradeMethod = 'upgradeTo'.ucfirst($version);
      
      try
      {
        $this->$upgradeMethod();
      }
      catch(Exception $e)
      {
        $this->logBlock('Can not upgrade to version '.$version.' : '.$e->getMessage(), 'ERROR');
      }
    }
  }
  
  protected function upgradeTo500ALPHA4()
  {
    dmDb::query()
    ->update('DmSetting')
    ->set('group_name', '?', 'Interface')
    ->where('group_name = ?', 'IHM')
    ->execute();
  }
  
}