<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot();

$t = new lime_test(16);

$v = new dmValidatorCssSize();

$t->diag('->clean()');
foreach (array(
  '0.2px',
  '3333',
  '0.3',
  '303%',
  '0.5em',
  '0ex',
  '88pt',
  '14cm'
) as $size)
{
  try
  {
    $t->comment('"'.$size.'" -> "'.$v->clean($size).'"');
    $t->pass('->clean() checks that the value is a valid css size');
  }
  catch (sfValidatorError $e)
  {
    $t->fail('->clean() checks that the value is a valid css size');
  }
}

foreach (array(
  ''
) as $nonSize)
{
  try
  {
    $v->clean($nonSize);
    $t->fail('->clean() throws an sfValidatorError if the value is empty');
    $t->skip('', 1);
  }
  catch (sfValidatorError $e)
  {
    $t->pass('->clean() throws an sfValidatorError if the value is empty');
    $t->is($e->getCode(), 'required', '->clean() throws a sfValidatorError');
  }
}

foreach (array(
  'a',
  '-10px'
) as $nonSize)
{
  try
  {
    $v->clean($nonSize);
    $t->fail('->clean() throws an sfValidatorError if the value is not a valid css size');
    $t->skip('', 1);
  }
  catch (sfValidatorError $e)
  {
    $t->pass('->clean() throws an sfValidatorError if the value is not a valid css size');
    $t->is($e->getCode(), 'invalid', '->clean() throws a sfValidatorError');
  }
}

foreach (array(
  '8001px'
) as $nonSize)
{
  try
  {
    $v->clean($nonSize);
    $t->fail('->clean() throws an sfValidatorError if the value is out of range');
    $t->skip('', 1);
  }
  catch (sfValidatorError $e)
  {
    $t->pass('->clean() throws an sfValidatorError if the value is out of range');
    $t->is($e->getCode(), 'out_of_range', '->clean() throws a sfValidatorError');
  }
}