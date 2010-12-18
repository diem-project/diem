<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot();

$t = new lime_test(59);

$t->comment('iconv available : '.function_exists('iconv'));

$t->is(dmString::slugify(" phrâse avèc dés accënts "), $expected = "phrase-avec-des-accents", $expected);
$t->is(dmString::slugify("fonctionnalité"), $expected = "fonctionnalite", $expected);
$t->is(dmString::urlize(" phrâse avèc dés accënts "), $expected = "phrase-avec-des-accents", $expected);
$t->is(dmString::urlize("fonctionnalité"), $expected = "fonctionnalite", $expected);

$t->is(dmString::slugify("an-url.htm"), $expected = "an-url-htm", $expected);
$t->is(dmString::slugify("an-url.html"), $expected = "an-url-html", $expected);
$t->is(dmString::urlize("an-url.htm"), $expected = "an-url.htm", $expected);
$t->is(dmString::urlize("an-url.html"), $expected = "an-url.html", $expected);

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
  $t->is(dmString::hexColor($hexTest[0]), $hexTest[1], 'dmString::hexColor('.$hexTest[0].') = '.(null === $hexTest[1] ? 'NULL' : $hexTest[1]));
}

$t->is(dmString::lcfirst('TEST'), 'tEST', 'lcfirst test');

$t->is(dmString::lcfirst('another test'), 'another test', 'lcfirst test');

// ::retrieveOptFromString()
$t->diag('::retrieveOptFromString()');

// Empty string
$t->diag('  ::retrieveOptFromString() empty string');
$string = '';
$opt = array('aa' => 'bb');
$originalOpt = $opt;
$t->is_deeply(dmString::retrieveOptFromString($string, $opt), null, '::retrieveOptFromString() with an empty string returns null');
$t->is_deeply($opt, $originalOpt, '::retrieveOptFromString() with an empty string does not modify opt');
$t->is_deeply($string, '', '::retrieveOptFromString() with an empty string does not modify string');

// Non-empty string
$t->diag('  ::retrieveOptFromString() non-empty string');
$string = 'x=y';
$opt = array('aa' => 'bb');
dmString::retrieveOptFromString($string, $opt);

$t->is_deeply($opt, array('aa' => 'bb', 'x' => 'y'), '::retrieveOptFromString() merges the options');
$t->is_deeply($string, '', '::retrieveOptFromString() sets the string parameter to an empty string');

// string overwrites opt
$t->diag('  ::retrieveOptFromString() overwriting');
$string = 'x=string';
$opt = array('x' => 'opt');
dmString::retrieveOptFromString($string, $opt);

$t->is_deeply($opt, array('x' => 'string'), '::retrieveOptFromString() string has the precedence over opt');

// ::retrieveCssFromString()
$t->diag('::retrieveCssFromString');
$cssFromStringsTests = array(
  array('', array(), '', array(), 'empty string'),
  array('#an_id', array(), '', array('id' => 'an_id'), 'one id only'),
  array('#an_id', array('id' => 'old'), '', array('id' => 'an_id'), 'id in opts is overridden'),
  array('.a_class', array(), '', array('class' => array('a_class')), 'one class only'),
  array('.a_class.another_class', array(), '', array('class' => array('a_class', 'another_class')), 'multiple classes'),
  array('#an_id.a_class', array(), '', array('id' => 'an_id', 'class' => array('a_class')), 'an id and a class'),
  array('#an_id.a_class href="/page"', array(), ' href="/page"', array('id' => 'an_id', 'class' => array('a_class')), 'garbage string after'),
/*
 * this methodsupports mixed CSS and SF styles only if CSS style is before SF style.
 * the first space indicates end of CSS style and begining of SF style.
 */
//  array('href="/page" a#an_id.a_class', array(), 'href="/page" a', array('id' => 'an_id', 'class' => array('a_class')), 'garbage string before'),
  array('href="/page" a#an_id.a_class', array(), 'href="/page" a#an_id.a_class', array(), 'garbage string before'),
  array('#an_id alt="I am. Are you?"', array(), ' alt="I am. Are you?"', array('id' => 'an_id'), 'dots are not taken into account if not classes'),
  array('#an_id.imaclass alt="I am. Are you?"', array(), ' alt="I am. Are you?"', array('id' => 'an_id', 'class' => array('imaclass')), 'dots are not taken into account if not classes'),
  array('#an_id.imaclass alt="I am. Are you? and.imaclass"', array(), ' alt="I am. Are you? and.imaclass"', array('id' => 'an_id', 'class' => array('imaclass')), 'dots are not taken into account if not classes, and a class with same name exists'),
  array('.cls href="#anchor"', array(), ' href="#anchor"', array('class' => array('cls')), '# are not taken into account if not ids'),
  array('.cls href="page#anchor"', array(), ' href="page#anchor"', array('class' => array('cls')), '# are not taken into account if not ids, even if they have text before'),
);

foreach($cssFromStringsTests as $cssFromStringsTest)
{
  list($str, $opts, $expectedStr, $expectedOpts, $msg) = $cssFromStringsTest;
  dmString::retrieveCssFromString($str, $opts);
  $t->comment('  ::retrieveCssFromString() '. $msg);
  $t->is_deeply($str, $expectedStr, '::retrieveCssFromString() ' . $msg . ': testing resulting string');
  $t->is_deeply($opts, $expectedOpts, '::retrieveCssFromString() ' . $msg . ': testing resulting opts');
}

// ::toArray()
$t->diag('::toArray()');

$t->is_deeply(dmString::toArray($arr = array('some' => 'array')), $arr, '::toArray() with an array returns the array');

$t->is_deeply(dmString::toArray(''), array(), '::toArray() with an empty string returns an empty array');

$t->is_deeply(dmString::toArray('#an_id.a_class.another_class'), array(
  'id' => 'an_id',
  'class' => array('a_class', 'another_class'),
), '::toArray() jquery style');

$t->is_deeply(dmString::toArray('an_option=a_value other_option=other_value'), array(
  'an_option' => 'a_value',
  'other_option' => 'other_value',
), '::toArray() symfony style');

$t->is_deeply(dmString::toArray('#an_id.a_class.another_class an_option=a_value other_option=other_value'), array(
  'id' => 'an_id',
  'class' => array('a_class', 'another_class'),
  'an_option' => 'a_value',
  'other_option' => 'other_value',
), '::toArray() with jquery AND symfony styles');

$t->is_deeply(dmString::toArray('#jquery id=symfony'), array(
  'id' => 'symfony',
), '::toArray() symfony style has precedence over jquery style');

$t->is_deeply(dmString::toArray('#an_id.a_class.another_class href=page#anchor'), array(
  'id' => 'an_id',
  'class' => array('a_class', 'another_class'),
  'href' => 'page#anchor',
), '::toArray() if a symfony style option contains a #');

$t->is_deeply(dmString::toArray('#an_id.a_class.another_class an_option=a_value other_option=other_value', true), array(
  'id' => 'an_id',
  'class' => 'a_class another_class',
  'an_option' => 'a_value',
  'other_option' => 'other_value',
), '::toArray() with implodeClasses = true');

$t->is_deeply(dmString::toArray('action="http://site.com/url"'), array(
  'action' => 'http://site.com/url'
), 'correctly extract action');

$t->is_deeply(dmString::toArray('.class action="http://site.com/url"'), array(
  'class' => array('class'),
  'action' => 'http://site.com/url'
), 'correctly extract action and class');

$t->is_deeply(dmString::toArray('#id.class action="http://site.com/url"'), array(
  'id' => 'id',
  'action' => 'http://site.com/url',
  'class' => array('class')
), 'correctly extract action, id and class');