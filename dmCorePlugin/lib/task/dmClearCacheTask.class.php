<?php

/**
 * Install Diem
 */
class dmClearCacheTask extends dmServiceTask
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
    return $this->executeService("dmClearCache", $options);
  }
}
