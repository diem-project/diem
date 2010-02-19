<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot();

$t = new lime_test(38);

$v = new dmValidatorCssClasses();

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
  '.a b.c.d'
) as $classes)
{
  try
  {
    $t->comment('"'.$classes.'" -> "'.$v->clean($classes).'"');
    $t->pass('->clean() checks that the value is a valid css class name');
  }
  catch (sfValidatorError $e)
  {
    $t->fail('->clean() checks that the value is a valid css class name');
  }
}

foreach (array(
  '.zegze$g.zegf',
  '/',
  'a/f',
  'a^',
  'a#',
  'é',
  '-{',
  '#myid.a b.c.d',
  '.a b#myid.c.d',
  '.a b#myid.c.d#myid',
  '.a b#myid.c.d  #myid',
  '#my_id',
  '#my-id',
  ' #my-id  '
) as $nonClass)
{
  try
  {
    $v->clean($nonClass);
    $t->fail('->clean() throws an sfValidatorError if the value is not a valid css class name');
    $t->skip('', 1);
  }
  catch (sfValidatorError $e)
  {
    $t->pass('->clean() throws an sfValidatorError if the value is not a valid css class name');
    $t->is($e->getCode(), 'invalid', '->clean() throws a sfValidatorError');
  }
}