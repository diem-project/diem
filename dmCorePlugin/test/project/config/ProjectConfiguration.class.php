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
      'dmContactPlugin',
      'dmTagPlugin'
    ));

    $this->setWebDir(realpath(dirname(__FILE__).'/../public_html'));

    $this->dispatcher->disconnect('dm.setup.after', array($this, 'listenToSetupAfterEvent'));
    $this->dispatcher->connect('dm.setup.after', array($this, 'listenToSetupAfterEvent'));
  }

  public function setupPlugins()
  {
    $this->pluginConfigurations['dmCorePlugin']->connectTests();
    $this->pluginConfigurations['dmAlternativeHelperPlugin']->connectTests();
    $this->pluginConfigurations['dmContactPlugin']->connectTests();
    $this->pluginConfigurations['dmUserPlugin']->connectTests();
  }
  
  public function listenToSetupAfterEvent(sfEvent $event)
  {
    $this->cleanupUploads($event->getSubject()->getFilesystem());
    
    dmDb::table('DmMediaFolder')->checkRoot()->sync();
    dmDb::table('DmPage')->checkBasicPages();

    if(!sfConfig::get('dm_test_project_built') && $event['clear-db'])
    {
      $event->getSubject()->getContext()->get('filesystem')->sf('my:project-builder');
    }
    
    if(file_exists(dmOs::join(sfConfig::get('sf_data_dir'), 'db.sqlite')))
    {
	    copy(dmOs::join(sfConfig::get('sf_data_dir'), 'db.sqlite'), dmOs::join(sfConfig::get('sf_data_dir'), 'fresh_db.sqlite'));
    }
    $this->removeWebSymlinks();
    
    sfConfig::set('dm_test_project_built', true);
  }

  protected function removeWebSymlinks()
  {
    foreach(array(
      'sfFormExtraPlugin', 'dmCorePlugin', 'dmAdminPlugin', 'dmFrontPlugin', 'dmUserPlugin', 'sf', 'dmTagPlugin'
    ) as $symlink)
    {
      @unlink(dmOs::join(sfConfig::get('sf_web_dir'), $symlink));
    }
  }
  
  public function cleanup(sfFilesystem $filesystem)
  {
    sfToolkit::clearDirectory(sfConfig::get('sf_log_dir'));
    sfToolkit::clearDirectory(dmOs::join(sfConfig::get('sf_web_dir'), 'cache'));
    sfToolkit::clearDirectory(dmOs::join(sfConfig::get('sf_root_dir'), 'cache'));
    sfToolkit::clearDirectory(sfConfig::get('sf_web_dir').'/themeAdmin');
    $filesystem->remove(sfFinder::type('any')->not_name('*.sqlite')->in(sfConfig::get('sf_data_dir')));
    $filesystem->remove(sfFinder::type('file')->name('sitemap*')->in(sfConfig::get('sf_web_dir')));
    if(file_exists(dmOs::join(sfConfig::get('sf_data_dir'), 'fresh_db.sqlite')))
    {
    	copy(dmOs::join(sfConfig::get('sf_data_dir'), 'fresh_db.sqlite'), dmOs::join(sfConfig::get('sf_data_dir'), 'db.sqlite'));
    }
    $this->cleanupUploads($filesystem);
    $this->removeWebSymlinks();
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