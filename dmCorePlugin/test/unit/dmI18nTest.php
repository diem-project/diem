<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot();

$t = new lime_test();

$i18n = $helper->get('i18n');
$user = $helper->get('user');

$i18n->setCultures(array_merge(
  $i18n->getCultures(),
  array('c1', 'c2')
));

$user->setCulture('en');

$t->diag('Clearing settings translations for cultures c1 and c2');


$form = new DmSigninAdminForm;

$t->is($i18n->__('Username'), $expected = 'Username', 'en: '.$expected);
$t->is((string)$form['username']->label(), $expected = '<label class="label" for="signin_username">Username</label>', 'en: '.$expected);

$user->setCulture('c1');

$t->is($i18n->__('Username'), $expected = 'Username', 'c1: '.$expected);
$t->is((string)$form['username']->label(), $expected = '<label class="label" for="signin_username">Username</label>', 'c1: '.$expected);

$t->comment('Translate Username for c1');

$i18n->addTranslations('c1', array(
  'Username' => 'c1 Username'
));

$t->is($i18n->__('Username', array(), 'messages'), $expected = 'c1 Username', 'c1: '.$expected);
$t->is((string)$form['username']->renderLabel(), $expected = '<label for="signin_username">c1 Username</label>', 'c1: '.$expected);
$t->is((string)$form['username']->label(), $expected = '<label class="label" for="signin_username">c1 Username</label>', 'c1: '.$expected);