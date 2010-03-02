<?php

class dmSyncPagesTask extends dmContextTask
{

  /**
   * @see sfTask
   */
  protected function configure()
  {
    parent::configure();
    
    $this->namespace = 'dm';
    $this->name = 'sync-pages';
    $this->briefDescription = 'Synchronize pages according to modules and records.';

    $this->detailedDescription = $this->briefDescription;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $this->withDatabase();
    
    $treeWatcher = $this->get('page_tree_watcher')->setOption('use_thread', false);
    
    $this->logSection('diem', 'Synchronize pages... this may take some time');

    $startTime = microtime(true);
    $treeWatcher->synchronizePages();
    $this->logSection('diem', sprintf('%d pages synchronized in %s ms', dmDb::table('DmPage')->count(), round(1000*(microtime(true) - $startTime))));
    
    $this->logSection('diem', 'Synchronize SEO... this may take some time');

    $startTime = microtime(true);
    $treeWatcher->synchronizeSeo();
    $this->logSection('diem', sprintf('%d page translations synchronized in %s ms', dmDb::table('DmPageTranslation')->count(), round(1000*(microtime(true) - $startTime))));

    if (count($this->get('i18n')->getCultures()) > 1)
    {
      $this->logSection('diem', 'Create missing page translations... this may take some time');
      $startTime = microtime(true);
      $this->get('page_i18n_builder')->createAllPagesTranslations();
      $this->logSection('diem', sprintf('Finished in %s ms', round(1000*(microtime(true) - $startTime))));
    }
  }
}