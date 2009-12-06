<?php

require_once(dirname(__FILE__).'/helper/dmTestHelper.php');
$helper = new dmTestHelper();
$helper->boot();

$t = new lime_test();

$user  = $helper->get('user');
$theme = $user->getTheme();

$user->setCulture('en');

dm::loadHelpers(array('Dm'));

$imageFullPath = $theme->getFullPath('images/testImage.jpg');
copy(
dmOs::join(sfConfig::get('dm_core_dir'), 'data/image/defaultMedia.jpg'),
$imageFullPath
);

$t->ok(file_exists($imageFullPath), 'image copied : '.$imageFullPath);

$imageI18nFullPath = $theme->getFullPath('images/en/testImage.jpg');
$helper->get('filesystem')->mkdir(dirname($imageI18nFullPath));
copy(
dmOs::join(sfConfig::get('dm_core_dir'), 'data/image/defaultMedia.jpg'),
$imageI18nFullPath
);

$t->ok(file_exists($imageI18nFullPath), 'image copied : '.$imageI18nFullPath);

$tag = £media('images/testImage.jpg');
$t->is($tag->getSrc(), $theme->getWebPath('images/testImage.jpg'), 'images/testImage.jpg : '.$tag->getSrc());

$tag = £media('images/en/testImage.jpg');
$t->is($tag->getSrc(), $theme->getWebPath('images/en/testImage.jpg'), 'images/en/testImage.jpg : '.$tag->getSrc());

$tag = £media('images/%culture%/testImage.jpg');
$t->is($tag->getSrc(), $theme->getWebPath('images/en/testImage.jpg'), 'images/%culture%/testImage.jpg : '.$tag->getSrc());

$tag = £media('testImage.jpg');
$t->is($tag->getSrc(), $theme->getWebPath('images/testImage.jpg'), 'testImage.jpg : '.$tag->getSrc());

$tag = £media('%culture%/testImage.jpg');
$t->is($tag->getSrc(), $theme->getWebPath('images/en/testImage.jpg'), '%culture%/testImage.jpg : '.$tag->getSrc());

$tag = £media($imageI18nFullPath);
$t->is($tag->getSrc(), $theme->getWebPath('images/en/testImage.jpg'), '$imageI18nFullPath : '.$tag->getSrc());

$user->setCulture('fr');

$imageI18nFullPath = $theme->getFullPath('images/fr/testImage.jpg');
$helper->get('filesystem')->mkdir(dirname($imageI18nFullPath));
copy(
dmOs::join(sfConfig::get('dm_core_dir'), 'data/image/defaultMedia.jpg'),
$imageI18nFullPath
);

$t->ok(file_exists($imageI18nFullPath), 'image copied : '.$imageI18nFullPath);

$tag = £media('images/%culture%/testImage.jpg');
$t->is($tag->getSrc(), $theme->getWebPath('images/fr/testImage.jpg'), 'images/%culture%/testImage.jpg : '.$tag->getSrc());

$tag = £media('%culture%/testImage.jpg');
$t->is($tag->getSrc(), $theme->getWebPath('images/fr/testImage.jpg'), '%culture%/testImage.jpg : '.$tag->getSrc());

$t->diag('tag generation tests');

sfConfig::set('dm_accessibility_image_empty_alts', true);
$tag = £media('images/testImage.jpg');
$webPath = $theme->getWebPath('images/testImage.jpg');
$expected = '<img src="'.$webPath.'" alt="" />';
$t->is($tag->render(), $expected, '$tag->render() is '.$expected);

sfConfig::set('dm_accessibility_image_empty_alts', false);
$tag = £media('images/testImage.jpg');
$webPath = $theme->getWebPath('images/testImage.jpg');
$expected = '<img src="'.$webPath.'" />';
$t->is($tag->render(), $expected, '$tag->render() is '.$expected);

$tag = £media('images/testImage.jpg');
$webPath = $theme->getWebPath('images/testImage.jpg');
$expected = '<img alt="this is the alt" src="'.$webPath.'" />';
$t->is($tag->alt('this is the alt')->render(), $expected, '$tag->alt(\'this is the alt\')->render() is '.$expected);

$tag = £media('images/testImage.jpg');
$webPath = $theme->getWebPath('images/testImage.jpg');
$expected = '<img width="200" height="150" src="'.$webPath.'" />';
$t->is($tag->size(200, 150)->render(), $expected, '$tag->size(200, 150)->render() is '.$expected);

$tag = £media('images/testImage.jpg');
$webPath = $theme->getWebPath('images/testImage.jpg');
$expected = '<img width="200" height="150" src="'.$webPath.'" />';
$t->is($tag->width(200)->height(150)->render(), $expected, '$tag->width(200)->height(150)->render() is '.$expected);

$tag = £media('images/testImage.jpg');
$webPath = $theme->getWebPath('images/testImage.jpg');
$expected = '<img width="200" src="'.$webPath.'" />';
$t->is($tag->width(200)->render(), $expected, '$tag->width(200)->render() is '.$expected);

$tag = £media('images/testImage.jpg');
$webPath = $theme->getWebPath('images/testImage.jpg');
$expected = '<img height="150" src="'.$webPath.'" />';
$t->is($tag->height(150)->render(), $expected, '$tag->height(150)->render() is '.$expected);

$tag = £media('images/testImage.jpg');
$webPath = $theme->getWebPath('images/testImage.jpg');
$expected = '<img src="'.$webPath.'" />';
$t->is($tag->method('inflate')->background('#000000')->quality(80)->filter('greyscale')->render(), $expected, '$tag->method(\'inflate\')->background(\'#000000\')->quality(80)->filter(\'greyscale\')->render() is '.$expected);