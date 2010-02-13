<?php

require_once realpath(dirname(__FILE__).'/../../../config/ProjectConfiguration.class.php');

$config = array(
  'login'     => false,   // whether to log a user or not at the beginning of the tests
  'username'  => 'admin', // username to log in
  'validate'  => false,   // perform html validation on each page, based on its doctype
  'debug'     => false,   // use debug mode ( slower, use more memory )
  'env'       => 'test'   // sf_environment when running tests
);

ProjectConfiguration::getApplicationConfiguration('front', $config['env'], $config['debug']);

$test = new dmFrontFunctionalCoverageTest($config);

$test->run();