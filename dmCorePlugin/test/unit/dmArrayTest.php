<?php

require_once(dirname(__FILE__).'/helper/dmTestHelper.php');
$helper = new dmTestHelper();
$helper->boot();

$t = new lime_test(3);

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