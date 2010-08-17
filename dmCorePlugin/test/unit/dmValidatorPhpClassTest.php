<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot();

$t = new lime_test(11);

$v = new dmValidatorPhpClass();

$t->diag('->clean()');
foreach (array(
  'dmMenu',
  'dmSitemapMenu',
  'dmOs'
) as $classes)
{
  try
  {
    $t->comment('"'.$classes.'" -> "'.$v->clean($classes).'"');
    $t->pass('->clean() checks that the class exists');
  }
  catch (sfValidatorError $e)
  {
    $t->fail('->clean() checks that the class exists');
  }
}

foreach (array(
  'nonexistingclass'
) as $nonClass)
{
  try
  {
    $v->clean($nonClass);
    $t->fail('->clean() throws an sfValidatorError if the class does not exist');
    $t->skip('', 1);
  }
  catch (sfValidatorError $e)
  {
    $t->pass('->clean() throws an sfValidatorError if the class does not exist');
    $t->is($e->getCode(), 'notfound', '->clean() throws a sfValidatorError');
  }
}

$v = new dmValidatorPhpClass(array(
  'implements' => 'dmMenu'
));

$t->diag('->clean()');
foreach (array(
  'dmMenu',
  'dmSitemapMenu'
) as $classes)
{
  try
  {
    $t->comment('"'.$classes.'" -> "'.$v->clean($classes).'"');
    $t->pass('->clean() checks that the class exists and implements dmMenu');
  }
  catch (sfValidatorError $e)
  {
    $t->fail('->clean() checks that the class exists and implements dmMenu');
  }
}

foreach (array(
  'nonexistingclass',
  'dmOs'
) as $nonClass)
{
  try
  {
    $v->clean($nonClass);
    $t->fail('->clean() throws an sfValidatorError if the class does not exist or does not implement dmMenu');
    $t->skip('', 1);
  }
  catch (sfValidatorError $e)
  {
    $t->pass('->clean() throws an sfValidatorError if the class does not exist or does not implement dmMenu');
    $t->is($e->getCode(), class_exists($nonClass) ? 'notimplement' : 'notfound', '->clean() throws a sfValidatorError');
  }
}