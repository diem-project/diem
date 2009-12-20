<?php

/*
 * Include Diem
 */
require_once '/home/thib/data/workspace/diemPlugin/trunk/dmCorePlugin/lib/core/dm.php';
dm::start();

class ProjectConfiguration extends dmProjectConfiguration
{

  public function setup()
  {
    parent::setup();
    
    $this->enablePlugins(array(
      // add plugins you want to enable here
    ));

    $this->setWebDir(realpath(dirname(__FILE__).'/../public_html'));
    
    $this->dispatcher->connect('dm.setup.after', array($this, 'listenToSetupAfterEvent'));
  }
  
  public function listenToSetupAfterEvent(sfEvent $event)
  {
    dmDb::table('DmMediaFolder')->checkRoot();
    dmDb::table('DmPage')->checkBasicPages();
    
    copy(dmOs::join(sfConfig::get('sf_data_dir'), 'db.sqlite'), dmOs::join(sfConfig::get('sf_data_dir'), 'fresh_db.sqlite'));
      
    unlink(dmOs::join(sfConfig::get('sf_web_dir'), 'dm/core'));
    unlink(dmOs::join(sfConfig::get('sf_web_dir'), 'dm/front'));
    unlink(dmOs::join(sfConfig::get('sf_web_dir'), 'dm/admin'));
    rmdir(dmOs::join(sfConfig::get('sf_web_dir'), 'dm'));
    unlink(dmOs::join(sfConfig::get('sf_web_dir'), 'sf'));
  }

  public function setupPlugins()
  {
    $this->pluginConfigurations['dmCorePlugin']->connectTests();
//    $this->pluginConfigurations['dmAlternativeHelperPlugin']->connectTests();
  }
}