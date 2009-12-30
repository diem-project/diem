<?php

/*
 * Include Diem
 */
require_once realpath(dirname(__FILE__).'/../../..').'/lib/core/dm.php';
dm::start();

class ProjectConfiguration extends dmProjectConfiguration
{

  public function setup()
  {
    parent::setup();
    
    $this->enablePlugins(array(
      'dmAlternativeHelperPlugin',
      'dmContactPlugin'
    ));

    $this->setWebDir(realpath(dirname(__FILE__).'/../public_html'));
    
    $this->dispatcher->connect('dm.setup.after', array($this, 'listenToSetupAfterEvent'));
  }

  public function setupPlugins()
  {
    $this->pluginConfigurations['dmCorePlugin']->connectTests();
    $this->pluginConfigurations['dmAlternativeHelperPlugin']->connectTests();
    $this->pluginConfigurations['dmContactPlugin']->connectTests();
  }
  
  public function listenToSetupAfterEvent(sfEvent $event)
  {
    $this->cleanupUploads($event->getSubject()->getFilesystem());
    
    dmDb::table('DmMediaFolder')->checkRoot()->sync();
    dmDb::table('DmPage')->checkBasicPages();
    
    copy(dmOs::join(sfConfig::get('sf_data_dir'), 'db.sqlite'), dmOs::join(sfConfig::get('sf_data_dir'), 'fresh_db.sqlite'));
    
    foreach(array('core', 'front', 'admin') as $assetDirName)
    {
      if(is_readable($assetDir = dmOs::join(sfConfig::get('sf_web_dir'), 'dm', $assetDirName)))
      {
        unlink($assetDir);
      }
    }
    
    if(is_readable($dmDir = dmOs::join(sfConfig::get('sf_web_dir'), 'dm')))
    {
      rmdir($dmDir);
    }
    
    if(is_readable($sfDir = dmOs::join(sfConfig::get('sf_web_dir'), 'sf')))
    {
      unlink($sfDir);
    }
  }
  
  public function cleanup(sfFilesystem $filesystem)
  {
//    sfToolkit::clearDirectory(sfConfig::get('sf_log_dir'));
    sfToolkit::clearDirectory(dmOs::join(sfConfig::get('sf_web_dir'), 'cache'));
    sfToolkit::clearDirectory(dmOs::join(sfConfig::get('sf_root_dir'), 'cache'));
    $filesystem->remove(sfFinder::type('any')->not_name('*.sqlite')->in(sfConfig::get('sf_data_dir')));
    copy(dmOs::join(sfConfig::get('sf_data_dir'), 'fresh_db.sqlite'), dmOs::join(sfConfig::get('sf_data_dir'), 'db.sqlite'));
    $this->cleanupUploads($filesystem);
  }
  
  protected function cleanupUploads(sfFilesystem $filesystem)
  {
    @sfToolkit::clearDirectory(sfConfig::get('sf_upload_dir'));
    $filesystem->mirror(
      dmOs::join(sfConfig::get('dm_core_dir'), 'test/fixtures/uploads'),
      sfConfig::get('sf_upload_dir'),
      sfFinder::type('any')
    );
  }
}