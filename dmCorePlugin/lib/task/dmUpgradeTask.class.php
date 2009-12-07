<?php

/**
 * Install Diem
 */
class dmUpgradeTask extends dmContextTask
{
  protected
  $diemVersions = array(
    '500ALPHA4',
    '500ALPHA6'
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
  
  /*
   * Rename IHM setting group
   */
  protected function upgradeTo500ALPHA4()
  {
    dmDb::query()
    ->update('DmSetting')
    ->set('group_name', '?', 'Interface')
    ->where('group_name = ?', 'IHM')
    ->execute();
  }
  
  /*
   * Fix login and secure module/action in admin/front settings.yml
   */
  protected function upgradeTo500ALPHA6()
  {
    // Admin : Replace login_module: login by signin
    $settingsFile = dmProject::rootify('apps/admin/config/settings.yml');
    $settingsText = file_get_contents($settingsFile);
    $settings = sfYaml::load($settingsText);
    
    $loginModule = dmArray::get(dmArray::get($settings['all'], '.settings'), 'login_module', array());
    $loginAction = dmArray::get(dmArray::get($settings['all'], '.settings'), 'login_action', array());
    
    if('dmAuth' == $loginModule && 'login' == $loginAction)
    {
      $settingsText = str_replace('login_action:           login', 'login_action:           signin',       $settingsText);
      file_put_contents($settingsFile, $settingsText);
    }
    
    // Front : Replace login_module: login by signin
    $settingsFile = dmProject::rootify('apps/front/config/settings.yml');
    $settingsText = file_get_contents($settingsFile);
    $settings = sfYaml::load($settingsText);
    
    $loginModule = dmArray::get(dmArray::get($settings['all'], '.settings'), 'login_module', array());
    
    if('dmAuth' == $loginModule)
    {
      $settingsText = str_replace(
'    secure_module:          dmAuth
    secureAction:           secure
    
    login_module:           dmAuth
    login_action:           login',
      
'    secure_module:          dmFront
    secure_action:          secure
    
    login_module:           dmFront
    login_action:           login', $settingsText);
      
      file_put_contents($settingsFile, $settingsText);
    }
  }
  
}