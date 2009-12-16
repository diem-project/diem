<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot();

$t = new lime_test(26);

$v = new dmValidatorDirectoryName();

$t->diag('->clean()');
foreach (array(
  'a',
  'a_b',
  'a-c',
  'qieurgfbqoiuzbfvoqiuzZFZGPSOZDNZKFjflzkh986875OoihzyfvbxoquyfvxqozufyxqzUEFV',
  '9',
  '_'
) as $dirName)
{
  $t->is($v->clean($dirName), $dirName, '->clean() checks that the value is a valid directory name');
}

foreach (array(
  ' ',
  'zegzeg zegf',
  ' dir',
  'dir ',
  '/',
  'a/f',
  'a^',
  'a#',
  'é',
  '-'
) as $nonDirName)
{
  try
  {
    $v->clean($nonDirName);
    $t->fail('->clean() throws an sfValidatorError if the value is not a valid directory name');
    $t->skip('', 1);
  }
  catch (sfValidatorError $e)
  {
    $t->pass('->clean() throws an sfValidatorError if the value is not a valid directory name');
    $t->is($e->getCode(), 'invalid', '->clean() throws a sfValidatorError');
  }
}