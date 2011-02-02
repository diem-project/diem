<?php

require_once(realpath(dirname(__FILE__).'/../../..').'/unit/helper/dmModuleUnitTestHelper.php');
$helper = new dmModuleUnitTestHelper();
$helper->boot('front');

$t = new lime_test(21);

$manager = $helper->get('theme_manager');

$default = $manager->getDefaultTheme();
$t->isa_ok($default, 'dmTheme', 'default theme is a dmTheme');
$t->is($default->getName(), 'Fancy Theme', 'default theme name is Fancy Theme');
$t->is($default->getPath(), '/fancyTheme/', 'default theme path is fancyTheme');
$t->ok($default->isEnabled(), 'default theme is enabled');
$t->ok($default->exists(), 'Fancy Theme exists');

$first = $manager->getTheme('First Theme');
$t->isa_ok($first, 'dmTheme', 'first theme is a dmTheme');
$t->is($first->getName(), 'First Theme', 'first theme name is First Theme');
$t->is($first->getPath(), '/theme/', 'first theme path is theme');
$t->ok(!$first->isEnabled(), 'first theme is not enabled');
$t->ok(!$first->exists(), 'First Theme does not exist');

$another = $manager->getTheme('Another Theme');
$t->isa_ok($another, 'dmTheme', 'another theme is a dmTheme');
$t->is($another->getName(), 'Another Theme', 'another theme name is Fancy Theme');
$t->is($another->getPath(), '/anotherTheme/', 'another theme path is fancyTheme');
$t->ok($another->isEnabled(), 'another theme is enabled');
$t->ok(!$another->exists(), 'Another Theme does not exist');

$t->is_deeply(
  $manager->getThemeNames(),
  $expected = array('First Theme', 'Fancy Theme', 'Another Theme'),
  'Theme names are '.implode(', ', $expected)
);

$t->is($manager->getDefaultThemeName(), 'Fancy Theme', 'default theme name is Fancy Theme');

$t->is($manager->getDefaultTheme(), $default, 'default theme is Fancy Theme');

$t->ok($manager->themeNameExists('Fancy Theme'), 'Fancy Theme exists');
$t->ok(!$manager->themeNameExists('Uncool Theme'), 'Uncool Theme does not exist');

$t->is_deeply(
  $manager->getThemesEnabled(),
  $expected = array(
    'Fancy Theme' => $default,
    'Another Theme' => $another
  ),
  'themes enabled are '.implode(', ', array_keys($expected))
);
