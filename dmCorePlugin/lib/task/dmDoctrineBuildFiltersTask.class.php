<?php

/*
 * Je modifie la classe sfPropelFormFilterGenerator en dmPropelFormFilterGenerator
 * qui, lui, va gérer l'internationalisation de :
 * label "is empty"
 * options "yes", "no", "yes or no"
 */

class dmDoctrineBuildFiltersTask extends sfDoctrineBuildFiltersTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
  	parent::configure();
    $this->namespace = 'dm';
    $this->aliases = array();
  }

}