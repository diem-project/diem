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
      new sfCommandArgument('domain', sfCommandArgument::REQUIRED, 'The domain name (ie. http://www.my-domain.com)'),
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

    if(0 !== strpos($arguments['domain'], 'http://'))
    {
      $arguments['domain'] = 'http://'.$arguments['domain'];
    }
    
    $this->log('Sitemap update');
    
    $this
    ->get('xml_sitemap_generator')
    ->setOption('domain', trim($arguments['domain'], '/'))
    ->execute();
    
    $this->log('The sitemap has been successfully generated');
  }
}