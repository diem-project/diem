<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot();

$t = new lime_test();

$mtr = $helper->get('mime_type_resolver');

$t->is(
  $mtr->getByFilename($file = __FILE__),
  $mt = 'application/x-httpd-php',
  $file.' : '.$mt
);

$t->is(
  $mtr->getGroupByFilename($file = __FILE__),
  $mt = 'application',
  $file.' : '.$mt
);

$t->is(
  $mtr->getByExtension($ext = 'php'),
  $mt = 'application/x-httpd-php',
  $ext.' : '.$mt
);

$t->is(
  $mtr->getGroupByExtension($ext = 'php'),
  $mt = 'application',
  $ext.' : '.$mt
);