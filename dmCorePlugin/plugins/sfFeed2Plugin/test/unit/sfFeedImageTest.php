<?php

define('SF_ROOT_DIR', realpath(dirname(__FILE__).'/../../../..'));
define('SF_APP',      'frontend');
include(dirname(__FILE__).'/../../../../test/bootstrap/functional.php');

$b = new sfTestBrowser();
$b->initialize();

$t = new lime_test(9, new lime_output_color());

$image_params = array(
  'title' => 'symfony project',
  'link' => 'http://www.symfony-project.org',
  'favicon' => 'http://www.symfony-project.org/favicon.ico', 
  'image' => 'http://www.symfony-project.org/images/symfony_logo.gif',
  'faviconX' => '16',
  'faviconY' => '16',
  'imageX' => '176',
  'imageY' => '37',
);

$item = new sfFeedImage();
$t->isa_ok($item->initialize($image_params), 'sfFeedImage', 'initialize() returns the current feed image object');
$t->is($item->getTitle(), $image_params['title'], 'getTitle() gets the feed image title');
$t->is($item->getLink(), $image_params['link'], 'getLink() gets the feed image link');
$t->is($item->getFavicon(), $image_params['favicon'], 'getFavicon() gets the feed favicon url');
$t->is($item->getImage(), $image_params['image'], 'getImage() gets the feed image url');
$t->is($item->getFaviconX(), $image_params['faviconX'], 'getFaviconX() gets the feed favicon x size');
$t->is($item->getFaviconY(), $image_params['faviconY'], 'getFaviconY() gets the feed favicon y size');
$t->is($item->getImageX(), $image_params['imageX'], 'getImageX() gets the feed image x size');
$t->is($item->getImageY(), $image_params['imageY'], 'getImageY() gets the feed image y size');

