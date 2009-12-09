<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot('admin');

if(sfConfig::get('sf_app') == 'admin' && class_exists('dmAdminPluginConfiguration', false))
{
  $t = new lime_test(4);
}
else
{
  $t = new lime_test(1);
  $t->pass('Works only on admin app');
  return;
}

dm::loadHelpers(array('Dm'));

$scriptName = $helper->get('request')->getRelativeUrlRoot();
$t->diag('Current cli script name = '.$scriptName);

$expected = $helper->get('controller')->genUrl('@homepage');
$t->is(£link()->getHref(), $expected, 'empty source is '.$expected);
$t->is(£link()->getHref('@homepage'), $expected, 'homepage href is '.$expected);

$expected = $helper->get('controller')->genUrl('dmAuth/signin');
$t->is(£link('+/dmAuth/signin')->getHref(), $expected, '+/dmAuth/signin href is '.$expected);

$expected = $helper->get('controller')->genUrl('dmAuth/signin');
$t->is($helper->get('helper')->£link('+/dmAuth/signin')->getHref(), $expected, 'with helper service, +/dmAuth/signin href is '.$expected);