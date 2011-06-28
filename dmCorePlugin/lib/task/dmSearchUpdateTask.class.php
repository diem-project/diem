<?php

class dmSearchUpdateTask extends dmContextTask
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
    $this->withDatabase();
    
    $this->log('Search engine index update');
    
    $this->get('service_container')->setService('logger', new sfConsoleLogger($this->dispatcher));
    
    $this->get('search_engine')->populate();
    $this->get('search_engine')->optimize();
  }
}