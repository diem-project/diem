<?php
require_once realpath(dirname(__FILE__).'/../../../config/ProjectConfiguration.class.php');
require_once dm::getDir().'/dmCorePlugin/lib/test/dmFrontFunctionalCoverageTest.php';

$config = array(
  'login'     => false,   // whether to log a user or not at the beginning of the tests
  'username'  => 'admin', // username to log in
  'validate'  => false,   // perform html validation on each page, based on its doctype
  'debug'     => true,    // use debug mode ( slower, use more memory )
  'env'       => 'prod'   // sf_environment when running tests
);

$test = new dmFrontFunctionalCoverageTest($config);

$test->run();