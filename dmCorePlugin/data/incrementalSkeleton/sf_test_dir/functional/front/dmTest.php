<?php

require_once realpath(dirname(__FILE__).'/../../../config/ProjectConfiguration.class.php');

$config = array(
  'env'       => 'test',
  'debug'     => true,
  'login'     => false,
  'username'  => 'admin',
  'password'  => 'admin'
);

$test = new dmFrontFunctionalCoverageTest($config);

$test->run();