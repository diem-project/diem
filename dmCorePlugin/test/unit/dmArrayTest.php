<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot();

$t = new lime_test(21);


$t->diag('first');

$t->is_deeply(
  dmArray::first('alpha'),
  'alpha',
  'first parameter is not an array'
);

$t->is_deeply(
  dmArray::first(array()),
  null,
  'empty source array'
);

$array = array('alpha', 'beta', 'gamma', 'delta');
$t->is_deeply(
  dmArray::first($array),
  'alpha',
  'first value'
);


$t->diag('firsts');

$t->is_deeply(
  dmArray::firsts('alpha', 2),
  'alpha',
  'first parameter is not an array'
);

$t->is_deeply(
  dmArray::firsts(array(), 2),
  null,
  'empty source array'
);

$t->is_deeply(
  dmArray::firsts(array('alpha'), 2),
  array('alpha'),
  'not enough values'
);

$array = array('alpha', 'beta', 'gamma', 'delta');
$t->is_deeply(
  dmArray::firsts($array, 2),
  array('alpha', 'beta'),
  'first values'
);


$t->diag('get');

$t->is_deeply(
  dmArray::get('alpha', null, 'test'),
  'test',
  'first parameter is not an array'
);

$t->is_deeply(
  dmArray::get(array('alpha'), 0, 'test'),
  'alpha',
  'key exists'
);

$t->is_deeply(
  dmArray::get(array('alpha'), 1, 'test'),
  'test',
  'key not exists'
);

$t->is_deeply(
  dmArray::get(array(''), 0, 'test', false),
  '',
  'empty value without default if empty'
);

$t->is_deeply(
  dmArray::get(array(''), 0, 'test', true),
  'test',
  'empty value with default if empty'
);

$t->is_deeply(
  dmArray::get(array('alpha'), 0, 'test', true),
  'alpha',
  'key exists with default if empty'
);


$t->diag('last');

$t->is_deeply(
  dmArray::last('alpha'),
  'alpha',
  'first parameter is not an array'
);

$t->is_deeply(
  dmArray::last(array()),
  null,
  'empty source array'
);

$array = array('alpha', 'beta', 'gamma', 'delta');
$t->is_deeply(
  dmArray::last($array),
  'delta',
  'last value'
);


$t->diag('toHtmlCssClasses');

$classes = array('class1', '', ' class2', ' ', '  class3  ');
$cleanClasses = 'class1 class2 class3';

$t->is(dmArray::toHtmlCssClasses($classes), $cleanClasses, 'clean classes : '.$cleanClasses);

$classes = array('class1');
$cleanClasses = 'class1';
$t->is(
  dmArray::toHtmlCssClasses($classes),
  $cleanClasses,
  'clean classes : '.$cleanClasses
);

$classes = array('  class1 class2 class3  ');
$cleanClasses = 'class1 class2 class3';
$t->is(
  dmArray::toHtmlCssClasses($classes),
  $cleanClasses,
  'clean classes : '.$cleanClasses
);


$t->diag('unsetEmpty');

$array = array(1 => '', 2 => 'delta', 3 => '', 'alpha' => 'gamma', 'gamma' => '', 'delta' => '');
$t->is_deeply(
  dmArray::unsetEmpty($array, array(3, 'gamma')),
  array('1' => '', '2' => 'delta', 'alpha' => 'gamma', 'delta' => ''),
  'check'
);


$t->diag('valueToKey');

$array = array('alpha', 'beta', 'gamma', 'delta');
$t->is_deeply(
  dmArray::valueToKey($array),
  array('alpha' => 'alpha', 'beta' => 'beta', 'gamma' => 'gamma', 'delta' => 'delta'),
  'check'
);
