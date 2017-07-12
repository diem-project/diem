<?php

define('SF_ROOT_DIR', realpath(dirname(__FILE__).'/../../../..'));
define('SF_APP',      'frontend');
include(dirname(__FILE__).'/../../../../test/bootstrap/functional.php');

$b = new sfTestBrowser();
$b->initialize();

$t = new lime_test(15, new lime_output_color());

$enclosureParams = array(
  'url' => 'foo.com', 
  'length' => '1234', 
  'mimeType' => 'foobarmimetype',
);
$enclosure = new sfFeedEnclosure();
$enclosure->initialize($enclosureParams);

$item_params = array(
  'title' => 'foo', 
  'link' => 'http://www.example.com',
  'description' => 'foobar baz',
  'content' => 'hey, do you foo, bar?',
  'authorName' => 'francois',
  'authorEmail' => 'francois@toto.com',
  'authorLink' => 'http://francois.toto.com',
  'pubDate' => '12345',
  'comments' => 'this is foo bar baz',
  'uniqueId' => 'hello world',
  'enclosure' => $enclosure,
  'categories' => array('foo', 'bar'),
);

$item = new sfFeedItem();
$t->isa_ok($item->initialize($item_params), 'sfFeedItem', 'initialize() returns the current feed item object');
$t->is($item->getTitle(), $item_params['title'], 'getTitle() gets the item title');
$t->is($item->getLink(), $item_params['link'], 'getLink() gets the item link');
$t->is($item->getDescription(), $item_params['description'], 'getDescription() gets the item description');
$t->is($item->getContent(), $item_params['content'], 'getContent() gets the item content');
$t->is($item->getAuthorName(), $item_params['authorName'], 'getAuthorName() gets the item author name');
$t->is($item->getAuthorEmail(), $item_params['authorEmail'], 'getAuthorEmail() gets the item author email');
$t->is($item->getAuthorLink(), $item_params['authorLink'], 'getAuthorLink() gets the item author link');
$t->is($item->getPubDate(), $item_params['pubDate'], 'getPubDate() gets the item publication date');
$t->is($item->getComments(), $item_params['comments'], 'getComments() gets the item comments');
$t->is($item->getUniqueId(), $item_params['uniqueId'], 'getUniqueId() gets the item unique id');
$t->is($item->getEnclosure(), $item_params['enclosure'], 'getEnclosure() gets the item enclosure');
$t->is($item->getCategories(), $item_params['categories'], 'getCategories() gets the item categories');

$item_params = array(
  'title' => 'foo', 
  'link' => 'http://www.example.com',
  'content' => 'hey, do you <strong>foo</strong>, my dear bar?',
);
$item = new sfFeedItem();
$item->initialize($item_params);
$t->is($item->getDescription(), strip_tags($item_params['content']), 'getDescription() gets the stripped item content when no description is defined');
sfConfig::set('app_feed_item_max_length', 5);
$t->is($item->getDescription(), 'hey, [...]', 'getDescription() gets the stripped item content truncated to a maximaum size when no description is defined');