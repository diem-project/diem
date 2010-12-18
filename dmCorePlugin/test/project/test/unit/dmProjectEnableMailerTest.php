<?php

require_once(realpath(dirname(__FILE__).'/../../..').'/unit/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot();

$t = new lime_test();

$t->ok(!class_exists('Swift_Message'), 'Swift_Message class does not exist');

$t->info('Enable mailer');
dm::enableMailer();

$t->ok(class_exists('Swift_Message'), 'Swift_Message class now exists');