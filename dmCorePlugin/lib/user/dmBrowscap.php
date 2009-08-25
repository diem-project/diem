<?php

class dmBrowscap extends Browscap
{

  public function __construct($cacheDir, $localFile = null)
  {
  	$this->updateMethod = self::UPDATE_LOCAL;
  	$this->localFile = $localFile;

  	parent::__construct($cacheDir);
  }

}