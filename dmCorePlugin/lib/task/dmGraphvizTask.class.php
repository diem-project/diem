<?php

/**
 * Install Diem
 */
class dmGraphvizTask extends dmServiceTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    parent::configure();

    $this->namespace = 'dm';
    $this->name = 'graphviz';
    $this->briefDescription = 'Generates a chart in format png of current object model';

    $this->detailedDescription = 'Generates a chart in format png of current object model';
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    return $this->executeService("dmGraphviz", $options);
  }
}
