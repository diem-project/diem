<?php

class dmSitemapUpdateTask extends dmContextTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    parent::configure();
    
    $this->addArguments(array(
      new sfCommandArgument('domain', sfCommandArgument::REQUIRED, 'The domain name'),
    ));
    
    $this->namespace = 'dm';
    $this->name = 'sitemap-update';
    $this->briefDescription = 'Update sitemap';

    $this->detailedDescription = $this->briefDescription;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $this->withDatabase();
    
    $this->log('Sitemap update');
    
    $sitemap = $this->get('sitemap');
    $sitemap->setBaseUrl('http://'.$arguments['domain']);
    
    $sitemap->generate($this->get('user')->getCulture());
    
    $this->log('The sitemap has been successfully generated');
  }
}