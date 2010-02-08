<?php

/**
 * Install Diem
 */
class dmUpgradeTask extends dmContextTask
{
  protected
  $diemVersions = array(
    'addGaToken',
    'clearLogs',
    'authDmUserModule',
    'authDmUserAdminModule',
    'renameLoginPage'
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

  /**
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

  /**
   * Clear old school formatted logs
   */
  protected function upgradeToClearLogs()
  {
    foreach(array('request', 'event') as $logName)
    {
      $file = sfConfig::get('sf_data_dir').'/dm/log/'.$logName.'.log';

      if(file_exists($file) && false !== strpos(file_get_contents($file), '{"time":'))
      {
        $this->logSection('upgrade', 'Cleared old school formatted log '.$logName);
        file_put_contents($file, '');
      }
    }
  }

  /**
   * Fix login and secure module in front settings.yml
   */
  protected function upgradeToAuthDmUserModule()
  {
    // Front : Replace login and secure module: dmFront -> dmAuth
    $settingsFile = dmProject::rootify('apps/front/config/settings.yml');
    $settingsText = file_get_contents($settingsFile);
    $settings = sfYaml::load($settingsText);

    foreach(array('.settings', '.actions') as $space)
    {
      $loginModule  = dmArray::get(dmArray::get($settings['all'], $space, array()), 'login_module');
      $loginAction  = dmArray::get(dmArray::get($settings['all'], $space, array()), 'login_action');
      $secureModule = dmArray::get(dmArray::get($settings['all'], $space, array()), 'secure_module');

      if('dmFront' == $loginModule)
      {
        $settingsText = preg_replace('/login_module\:(\s*)dmFront/i', 'login_module:$1dmUser', $settingsText);
        file_put_contents($settingsFile, $settingsText);
      }
      if('login' == $loginAction)
      {
        $settingsText = preg_replace('/login_action\:(\s*)login/i', 'login_action:$1signin', $settingsText);
        file_put_contents($settingsFile, $settingsText);
      }
      if('dmFront' == $secureModule)
      {
        $settingsText = preg_replace('/secure_module\:(\s*)dmFront/i', 'secure_module:$1dmUser', $settingsText);
        file_put_contents($settingsFile, $settingsText);
      }
    }
  }

  /*
   * Fix login and secure module in admin settings.yml
   */
  protected function upgradeToAuthDmUserAdminModule()
  {
    // Admin : Replace login and secure module: dmAuthAdmin -> dmUserAdmin
    $settingsFile = dmProject::rootify('apps/admin/config/settings.yml');
    $settingsText = file_get_contents($settingsFile);
    $settings = sfYaml::load($settingsText);

    foreach(array('.settings', '.actions') as $space)
    {
      $loginModule  = dmArray::get(dmArray::get($settings['all'], $space, array()), 'login_module');
      $secureModule = dmArray::get(dmArray::get($settings['all'], $space, array()), 'secure_module');

      if('dmAuth' == $loginModule || 'dmAuthAdmin' == $loginModule)
      {
        $settingsText = preg_replace('/login_module\:(\s*)\w+/i', 'login_module:$1dmUserAdmin', $settingsText);
        file_put_contents($settingsFile, $settingsText);
      }
      if('dmAuth' == $secureModule || 'dmAuthAdmin' == $loginModule)
      {
        $settingsText = preg_replace('/secure_module\:(\s*)\w+/i', 'secure_module:$1dmUserAdmin', $settingsText);
        file_put_contents($settingsFile, $settingsText);
      }
    }
  }

  protected function upgradeToRenameLoginPage()
  {
    if ($page = dmDb::table('DmPage')->findOneByModuleAndAction('main', 'login'))
    {
      if(!dmDb::table('DmPage')->findOneByModuleAndAction('main', 'signin'))
      {
        $page->set('action', 'signin');
        $page->save();
      }
    }
  }
}