<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot('front');

$isFront = sfConfig::get('dm_context_type') == 'front';
$sc = $helper->get('service_container');

$t = new lime_test(2 + count(sfConfig::get('dm_i18n_cultures')) + ($isFront ? count(sfConfig::get('dm_theme_list')) : 0));

$user  = $helper->get('user');
$theme = $user->getTheme();

$t->is($sc->getParameter('user.culture'), $user->getCulture(), 'culture is synchronized to '.$user->getCulture());
$t->is($sc->getParameter('user.theme'), $user->getTheme(), 'theme is synchronized to '.$user->getTheme());

foreach(sfConfig::get('dm_i18n_cultures') as $culture)
{
  $user->setCulture($culture);
  $t->is($sc->getParameter('user.culture'), $user->getCulture(), 'culture is synchronized to '.$user->getCulture());
}

if($isFront)
{
  $themeManager = $sc->get('theme_manager');
  
  foreach($themeManager->getThemes() as $theme)
  {
    $user->setTheme($theme);
    $t->is($sc->getParameter('user.theme'), $user->getTheme(), 'theme is synchronized to '.$user->getTheme());
  }
}