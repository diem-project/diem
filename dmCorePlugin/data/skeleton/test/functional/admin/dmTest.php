<?php

require_once realpath(dirname(__FILE__).'/../../../config/ProjectConfiguration.class.php');

$config = array(
  'env'       => 'test',
  'debug'     => true,
  'login'     => true,
  'username'  => 'admin',
  'password'  => ##DIEM_PROJECT_PASSWORD##,
  'validate'  => true
);

$test = new dmAdminFunctionalCoverageTest($config);

$test->run();