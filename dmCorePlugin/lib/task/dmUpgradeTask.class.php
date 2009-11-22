<?php

/**
 * Install Diem
 */
class dmUpgradeTask extends dmContextTask
{
  protected
  $diemVersions = array(
    '500dev1'
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
  
  protected function upgradeTo500Dev1()
  {
    // rename setting image_quality to image_resize_quality
    if ($setting = dmDb::table('DmSetting')->findOneByName('image_quality'))
    {
      if (!dmDb::query('DmSetting s')->where('s.name = ?', 'image_resize_quality')->exists())
      {
        $setting->name = 'image_resize_quality';
        $setting->save();
        $this->logBlock('renamed setting image_quality to image_resize_quality', 'INFO');
      }
    }
  }
}