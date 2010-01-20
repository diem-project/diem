<?php

/**
 * Install Diem
 */
class dmUpgradeTask extends dmContextTask
{
  protected
  $diemVersions = array(
    '500ALPHA4',
    '500ALPHA6',
    'deprecateMediaWidgets',
    'addGaToken'
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
    
    $loginModule = dmArray::get(dmArray::get($settings['all'], '.settings', array()), 'login_module');
    $loginAction = dmArray::get(dmArray::get($settings['all'], '.settings', array()), 'login_action');
    
    if('dmAuth' == $loginModule && 'login' == $loginAction)
    {
      $settingsText = preg_replace('/login_action\:(\s*)login/', 'login_action:$1signin', $settingsText);
      file_put_contents($settingsFile, $settingsText);
    }
    
    // Front : Replace secure_module, secureAction, login_module and login_action
    $settingsFile = dmProject::rootify('apps/front/config/settings.yml');
    $settingsText = file_get_contents($settingsFile);
    $settings = sfYaml::load($settingsText);
    
    $loginModule = dmArray::get(dmArray::get($settings['all'], '.settings', array()), 'login_module');
    
    if('dmAuth' == $loginModule)
    {
      $settingsText = preg_replace('/secure_module\:(\s*)dmAuth/', 'secure_module:$1dmFront', $settingsText);
      $settingsText = preg_replace('/secureAction\:(\s*)secure/', 'secure_action:$1secure', $settingsText);
      $settingsText = preg_replace('/login_module\:(\s*)dmAuth/', 'login_module:$1dmFront', $settingsText);
      
      file_put_contents($settingsFile, $settingsText);
    }
  }
  
  /*
   * Change dmWidgetContent.media widgets to dmWidgetContent.image widgets in database
   */
  protected function upgradeToDeprecateMediaWidgets()
  {
    dmDb::query()
    ->update('DmWidget')
    ->set('action', '?', 'image')
    ->where('module = ?', 'dmWidgetContent')
    ->andWhere('action = ?', 'media')
    ->execute();
  }

  /*
   * Add ga_token setting if missing
   */
  protected function upgradeToAddGaToken()
  {
    if(!dmConfig::has('ga_token'))
    {
      $setting = new DmSetting;
      $setting->set('name', 'ga_token');
      $setting->fromArray(array(
        'description' => 'Auth token gor Google Analytics, computed from password',
        'group_name'  => 'internal',
        'credentials' => 'google_analytics'
      ));

      $setting->save();
    }
  }
}