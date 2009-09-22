<?php

/**
 * Install Diem
 */
class dmSpriteTask extends dmServiceTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    parent::configure();
    $this->namespace = 'dm';
    $this->name = 'sprite';
    $this->briefDescription = 'Build css sprites';

    $this->detailedDescription = <<<EOF
Will build css sprites for diem core
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    dmContext::createInstance($this->configuration);
    return $this->executeService("dmSprite", $options);
  }
}
