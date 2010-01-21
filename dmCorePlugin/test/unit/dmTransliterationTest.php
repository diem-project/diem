<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot();

$t = new lime_test(21);

$helper->get('i18n')->loadTransliterationStrings(array('en', 'ru'));

$tests = array(
  'en' => array(
    'test' => 'test',
    'tèst' => 'test',
    'test...   /[)=' => 'test...   /[)=',
    'tèst' => 'test',
    'Í' => 'I',
    'Î' => 'I',
    'Ï' => 'I',
    'Ð' => 'D',
    'œÐú' => 'oeDu',
    'ж'=>'zh',
  ),
  'ru' => array(
    'test' => 'test',
    'tèst' => 'test',
    'test...   /[)=' => 'test...   /[)=',
    'tèst' => 'test',
    'Í' => 'I',
    'Î' => 'I',
    'Ï' => 'I',
    'Ð' => 'D',
    'œÐú' => 'oeDu',
    'ж'=>'zh',
    'з'=>'z',
    'и'=>'i',
    'й'=>'y'
  )
);

foreach($tests as $culture => $strings)
{
  $helper->get('user')->setCulture($culture);
  $t->comment('Current culture is '.$culture);

  foreach($strings as $source => $target)
  {
    $t->is(dmString::transliterate($source), $target, 'dmString::transliterate("'.$source.'") -> '.$target);
  }
}