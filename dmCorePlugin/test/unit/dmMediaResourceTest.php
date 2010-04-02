<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot();

$t = new lime_test(12);

$sc = $helper->get('service_container');

$user  = $helper->get('user');
$theme = $user->getTheme();

$user->setCulture('en');

$imageFullPath = $theme->getFullPath('images/testImage.jpg');
$sc->get('filesystem')->mkdir(dirname($imageFullPath));
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

$res = $sc->getService('media_resource')->initialize('images/testImage.jpg');
$t->is($res->getPathFromWebDir(), $theme->getPath('images/testImage.jpg'), 'images/testImage.jpg : '.$res->getPathFromWebDir());

$res = $sc->getService('media_resource')->initialize('images/en/testImage.jpg');
$t->is($res->getPathFromWebDir(), $theme->getPath('images/en/testImage.jpg'), 'images/en/testImage.jpg : '.$res->getPathFromWebDir());

$res = $sc->getService('media_resource')->initialize('images/%culture%/testImage.jpg');
$t->is($res->getPathFromWebDir(), $theme->getPath('images/en/testImage.jpg'), 'images/%culture%/testImage.jpg : '.$res->getPathFromWebDir());

$res = $sc->getService('media_resource')->initialize('testImage.jpg');
$t->is($res->getPathFromWebDir(), $theme->getPath('images/testImage.jpg'), 'testImage.jpg : '.$res->getPathFromWebDir());

$res = $sc->getService('media_resource')->initialize('%culture%/testImage.jpg');
$t->is($res->getPathFromWebDir(), $theme->getPath('images/en/testImage.jpg'), '%culture%/testImage.jpg : '.$res->getPathFromWebDir());

$res = $sc->getService('media_resource')->initialize($imageI18nFullPath);
$t->is($res->getPathFromWebDir(), $theme->getPath('images/en/testImage.jpg'), '$imageI18nFullPath : '.$res->getPathFromWebDir());

$user->setCulture('fr');

$imageI18nFullPath = $theme->getFullPath('images/fr/testImage.jpg');
$helper->get('filesystem')->mkdir(dirname($imageI18nFullPath));
copy(
dmOs::join(sfConfig::get('dm_core_dir'), 'data/image/defaultMedia.jpg'),
$imageI18nFullPath
);

$t->ok(file_exists($imageI18nFullPath), 'image copied : '.$imageI18nFullPath);

$res = $sc->getService('media_resource')->initialize('images/%culture%/testImage.jpg');
$t->is($res->getPathFromWebDir(), $theme->getPath('images/fr/testImage.jpg'), 'images/%culture%/testImage.jpg : '.$res->getPathFromWebDir());

$res = $sc->getService('media_resource')->initialize('%culture%/testImage.jpg');
$t->is($res->getPathFromWebDir(), $theme->getPath('images/fr/testImage.jpg'), '%culture%/testImage.jpg : '.$res->getPathFromWebDir());

$t->diag('Plugin medias');

$res = $sc->getService('media_resource')->initialize('dmCore/images/media/folder.png');
$expected = '/dmCorePlugin/images/media/folder.png';
$t->is($res->getPathFromWebDir(), $expected, 'dmCore/images/media/folder.png : '.$expected);