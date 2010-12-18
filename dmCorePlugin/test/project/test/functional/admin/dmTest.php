<?php

require_once realpath(dirname(__FILE__).'/../../../config/ProjectConfiguration.class.php');

$config = array(
  'login'     => true,    // whether to log a user or not at the beginning of the tests
  'username'  => 'admin', // username to log in
  'validate'  => false,   // perform html validation on each page, based on its doctype
  'debug'     => true,   // use debug mode ( slower, use more memory )
  'env'       => 'test'   // sf_environment when running tests
);

ProjectConfiguration::getApplicationConfiguration('admin', $config['env'], $config['debug']);

$test = new dmAdminFunctionalCoverageTest($config);

$test->run();