<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot();

$t = new lime_test(14);

$v = new dmValidatorYaml();

$t->diag('->clean()');
foreach (array(
  '[]',
  '{}',
  'a: val',
  'a: [ b, c ]',
  'a: { b: val, c: [ val, val ] }',
  'a:
  b:
    c:
      - val1
      - val2
    d: |
      a longer sentence
  e: true'
) as $yaml)
{
  $t->is($v->clean($yaml), $yaml, '->clean() checks that the value is a valid YAML declaration');
}

foreach (array(
  'a: {b}',
  'a:
b',
  'a: "c',
  'a:
  b: error
    c:
      - val1
      - val2
    d: |
      a longer sentence
  e: true'
) as $nonYaml)
{
  try
  {
    $v->clean($nonYaml);
    $t->fail('->clean() throws an sfValidatorError if the value is not a valid YAML declaration');
    $t->skip('', 1);
  }
  catch (sfValidatorError $e)
  {
    $t->pass('->clean() throws an sfValidatorError if the value is not a valid YAML declaration');
    $t->is($e->getCode(), 'invalid', '->clean() throws a sfValidatorError');
  }
}