<?php

class dmSearchUpdateTask extends dmBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_OPTIONAL, 'The application name', 'front'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod')
    ));
    
    $this->namespace = 'dm';
    $this->name = 'search-update';
    $this->briefDescription = 'Update search engine index';

    $this->detailedDescription = $this->briefDescription;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    if (!sfContext::hasInstance())
    {
      dm::createContext($this->configuration);
    }

    $databaseManager = new sfDatabaseManager($this->configuration);
    
    $this->log('Search engine index update');
    
    $index = dmContext::getInstance()->get('search_engine');
    $index->setLogger(new sfConsoleLogger($this->dispatcher));
    
    $index->populate();
    $index->optimize();
  }
}