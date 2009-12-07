<?php

require_once(dirname(__FILE__).'/helper/dmTestHelper.php');
$helper = new dmTestHelper();
$helper->boot('front');

$t = new lime_test(1);

$user = $helper->get('user');

$t->ok($helper->get('i18n')->cultureExists($user->getCulture()), 'The user\'s culture exists');