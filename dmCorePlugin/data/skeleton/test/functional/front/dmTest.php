<?php

require_once realpath(dirname(__FILE__).'/../../../config/ProjectConfiguration.class.php');

$config = array(
  'env'       => 'test',
  'debug'     => true,
  'login'     => false,
  'username'  => 'admin',
  'password'  => ##DIEM_PROJECT_PASSWORD##
);

$test = new dmFrontFunctionalCoverageTest($config);

$test->run();