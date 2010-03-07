<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot();

$t = new lime_test(32);

$user  = $helper->get('user');
$theme = $user->getTheme();
$deletables = array();

$user->setCulture('en');

dm::loadHelpers(array('Dm'));

$imageFullPath = $deletables[] = $theme->getFullPath('images/testImage.jpg');
$helper->get('filesystem')->mkdir(dirname($imageFullPath));
copy(
dmOs::join(sfConfig::get('dm_core_dir'), 'data/image/defaultMedia.jpg'),
$imageFullPath
);

$t->ok(file_exists($imageFullPath), 'image copied : '.$imageFullPath);

$imageI18nFullPath = $deletables[] = $theme->getFullPath('images/en/testImage.jpg');
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

$imageI18nFullPath = $deletables[] = $theme->getFullPath('images/fr/testImage.jpg');
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

$size = getimagesize(dmOs::join(sfConfig::get('sf_web_dir'), $helper->get('theme')->getPath('images/testImage.jpg')));

sfConfig::set('dm_accessibility_image_empty_alts', true);
$tag = £media('images/testImage.jpg');
$webPath = $theme->getWebPath('images/testImage.jpg');
$expected = sprintf('<img alt="" height="%d" src="%s" width="%d" />', $size[1], $webPath, $size[0]);
$t->is($tag->render(), $expected, '$tag->render() is '.$expected);

sfConfig::set('dm_accessibility_image_empty_alts', false);
$tag = £media('images/testImage.jpg');
$webPath = $theme->getWebPath('images/testImage.jpg');
$expected = sprintf('<img height="%d" src="%s" width="%d" />', $size[1], $webPath, $size[0]);
$t->is($tag->render(), $expected, '$tag->render() is '.$expected);

$tag = £media('images/testImage.jpg');
$webPath = $theme->getWebPath('images/testImage.jpg');
$expected = sprintf('<img alt="%s" height="%d" src="%s" width="%d" />', 'this is the alt', $size[1], $webPath, $size[0]);
$t->is($tag->alt('this is the alt')->render(), $expected, '$tag->alt(\'this is the alt\')->render() is '.$expected);

$tag = £media('images/testImage.jpg');
$webPath = $theme->getWebPath('images/testImage.jpg');
$expected = '<img height="150" src="'.$webPath.'" width="200" />';
$t->is($tag->size(200, 150)->render(), $expected, '$tag->size(200, 150)->render() is '.$expected);

$tag = £media('images/testImage.jpg');
$webPath = $theme->getWebPath('images/testImage.jpg');
$expected = '<img height="150" src="'.$webPath.'" width="200" />';
$t->is($tag->width(200)->height(150)->render(), $expected, '$tag->width(200)->height(150)->render() is '.$expected);

$tag = £media('images/testImage.jpg');
$webPath = $theme->getWebPath('images/testImage.jpg');
$expected = '<img src="'.$webPath.'" width="200" />';
$t->is($tag->width(200)->render(), $expected, '$tag->width(200)->render() is '.$expected);

$tag = £media('images/testImage.jpg');
$webPath = $theme->getWebPath('images/testImage.jpg');
$expected = '<img height="150" src="'.$webPath.'" />';
$t->is($tag->height(150)->render(), $expected, '$tag->height(150)->render() is '.$expected);

$tag = £media('images/testImage.jpg');
$webPath = $theme->getWebPath('images/testImage.jpg');
$expected = sprintf('<img height="%d" src="%s" width="%d" />', $size[1], $webPath, $size[0]);
$t->is($tag->method('inflate')->background('#000000')->quality(80)->filter('greyscale')->render(), $expected, '$tag->method(\'inflate\')->background(\'#000000\')->quality(80)->filter(\'greyscale\')->render() is '.$expected);

$t->comment('Create a test image media');

$mediaFileName = 'test_'.dmString::random().'.jpg';
copy(
  dmOs::join(sfConfig::get('dm_core_dir'), 'data/image/defaultMedia.jpg'),
  dmOs::join(sfConfig::get('sf_upload_dir'), $mediaFileName)
);
$media = dmDb::create('DmMedia', array(
  'file' => $mediaFileName,
  'dm_media_folder_id' => dmDb::table('DmMediaFolder')->checkRoot()->id
))->saveGet();

$t->ok($media->exists(), 'A test media has been created');

$rur = $helper->get('request')->getRelativeUrlRoot().'/';

$expected = sprintf('<img height="%d" src="%s" width="%d" />', $media->height, $rur.$media->webPath, $media->width);
$t->is(£media($media)->render(), $expected, $expected);
$t->is(£media('media:'.$media->id)->render(), $expected, $expected);
$t->is($helper->get('helper')->media('media:'.$media->id)->render(), $expected, $expected);

$expected = sprintf('#<img height="%d" src="[^"]+" width="%d" />#', 200, 300);
$t->like(£media($media)->size(300, 200)->render(), $expected, $expected);
$t->like(£media('media:'.$media->id)->size(300, 200)->render(), $expected, $expected);
$t->like($helper->get('helper')->media('media:'.$media->id)->size(300, 200)->render(), $expected, $expected);

$t->comment('With default alt');
$media->legend = 'The default alt';
$expected = sprintf('#<img alt="%s" height="%d" src="[^"]+" width="%d" />#', $media->legend, 200, 300);
$t->like(£media($media)->size(300, 200)->render(), $expected, $expected);

$t->comment('With remote media');
$url = 'http://www.symfony-project.org/images/symfony_logo.gif';
$t->is((string)£media($url), $expected = '<img src="'.$url.'" />', $expected);
$t->is((string)£media($url)->size(30, 20), $expected = '<img height="20" src="'.$url.'" width="30" />', $expected);

$t->comment('With remote media, url with parameter');
$url = 'http://www.symfony-project.org/images/symfony_logo.gif?param=value';
$t->is((string)£media($url), $expected = '<img src="'.$url.'" />', $expected);
$t->is((string)£media($url)->size(30, 20), $expected = '<img height="20" src="'.$url.'" width="30" />', $expected);

$tag = £media('non_existing');
$t->is($tag->render(), $tag->renderDefault(), 'non existent tag renders nothing');

$media->delete();

foreach($deletables as $deletable)
{
  $helper->get('filesystem')->remove($deletable);
}