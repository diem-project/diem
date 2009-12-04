<?php

require_once(dirname(__FILE__).'/helper/dmTestHelper.php');
$helper = new dmTestHelper();
$helper->boot();

$t = new lime_test(10);

$t->is(
  dmString::slugify(" phrâse avèc dés accënts "),
  "phrase-avec-des-accents",
  "phrase-avec-des-accents"
);

$t->is(
  dmString::slugify("fonctionnalité"),
  "fonctionnalite",
  "fonctionnalite"
);

$hexTests = array(
array('ffffff', 'FFFFFF'),
array('#ffffff', 'FFFFFF'),
array('#0Cd4fe', '0CD4FE'),
array('aaa', null),
array('fffff', null),
array('fffxff', null)
);
foreach($hexTests as $hexTest)
{
  $t->is(dmString::hexColor($hexTest[0]), $hexTest[1], 'dmString::hexColor('.$hexTest[0].') = '.$hexTest[1]);
}

$t->is(dmString::lcfirst('TEST'), 'tEST', 'lcfirst test');

$t->is(dmString::lcfirst('test'), 'test', 'lcfirst test');