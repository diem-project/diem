<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot();

$t = new lime_test(33);

$v = new dmValidatorCssIdAndClasses();

$t->diag('->clean()');
foreach (array(
  'a',
  'a_b',
  'a-c',
  'qieurgfbqoiuzbfvoqiuzZFZGPSOZDNZKFjflzkh986875OoihzyfvbxoquyfvxqozufyxqzUEFV',
  '9',
  '_',
  ' bla rebla  ',
  '- _ 8',
  '.class',
  '.a b.c.d',
  '#myid.a b.c.d',
  '#myid class1 class2',
  'class1 class2 #myid',
  '.a b#myid.c.d',
  '.a b#myid.c.d#myid',
  '.a b#myid.c.d  #myid',
  '#my_id',
  '#my-id',
  ' #my-id  '
) as $classes)
{
  try
  {
    $t->comment('"'.$classes.'" -> "'.$v->clean($classes).'"');
    $t->pass('->clean() checks that the value is a valid css class name + id');
  }
  catch (sfValidatorError $e)
  {
    $t->fail('->clean() checks that the value is a valid css class name + id');
  }
}

foreach (array(
  '.zegze$g.zegf',
  '/',
  'a/f',
  'a^',
  'a # @',
  'é',
  '-{'
) as $nonClass)
{
  $t->comment($nonClass);
  try
  {
    $v->clean($nonClass);
    $t->fail('->clean() throws an sfValidatorError if the value is not a valid css class name + id');
    $t->skip('', 1);
  }
  catch (sfValidatorError $e)
  {
    $t->pass('->clean() throws an sfValidatorError if the value is not a valid css class name + id');
    $t->is($e->getCode(), 'invalid', '->clean() throws a sfValidatorError');
  }
}