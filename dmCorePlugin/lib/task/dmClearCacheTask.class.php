<?php

/**
 * Install Diem
 */
class dmClearCacheTask extends dmContextTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    parent::configure();

    $this->aliases = array('ccc');
    $this->namespace = 'dm';
    $this->name = 'clear-cache';
    $this->briefDescription = 'Remove all cache dir content';

    $this->detailedDescription = <<<EOF
Will remove all cache dir content
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    if ($this->get('cache_manager')->clearAll())
    {
      $this->logSection('diem', 'Cache successfully cleared');
    }
    else
    {
      $this->logSection('diem', 'Some files can not be deleted. Please check permissions in /cache dir');
    }
  }
}
