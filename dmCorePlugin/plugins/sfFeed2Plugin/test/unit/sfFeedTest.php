<?php

include(dirname(__FILE__).'/../../../../test/bootstrap/unit.php');
require_once(dirname(__FILE__).'/../../lib/sfFeed.class.php');
require_once(dirname(__FILE__).'/../../lib/sfFeedItem.class.php');

$t = new lime_test(20, new lime_output_color());

$feed_params = array(
  'title' => 'foo', 
  'link' => 'bar', 
  'description' => 'foobar baz',
  'language' => 'fr', 
  'authorName' => 'francois',
  'authorEmail' => 'francois@toto.com',
  'authorLink' => 'http://francois.toto.com',
  'subtitle' => 'this is foo bar',
  'categories' => array('foo', 'bar'),
  'feedUrl' => 'http://www.example.com',
  'encoding' => 'UTF-16',
);

$feed = new sfFeed();
$t->isa_ok($feed->initialize($feed_params), 'sfFeed', 'initialize() returns the current feed object');
$t->is($feed->getTitle(), $feed_params['title'], 'getTitle() gets the feed title');
$t->is($feed->getLink(), $feed_params['link'], 'getLink() gets the feed link');
$t->is($feed->getDescription(), $feed_params['description'], 'getDescription() gets the feed description');
$t->is($feed->getLanguage(), $feed_params['language'], 'getLanguage() gets the feed language');
$t->is($feed->getAuthorName(), $feed_params['authorName'], 'getAuthorName() gets the feed author name');
$t->is($feed->getAuthorEmail(), $feed_params['authorEmail'], 'getAuthorEmail() gets the feed author email');
$t->is($feed->getAuthorLink(), $feed_params['authorLink'], 'getAuthorLink() gets the feed author link');
$t->is($feed->getSubtitle(), $feed_params['subtitle'], 'getSubtitle() gets the feed subtitle');
$t->is($feed->getCategories(), $feed_params['categories'], 'getCategories() gets the feed categories');
$t->is($feed->getFeedUrl(), $feed_params['feedUrl'], 'getFeedUrl() gets the feed url');
$t->is($feed->getEncoding(), $feed_params['encoding'], 'getEncoding() gets the feed encoding');
try
{
  $feed->addItem('foobar');
  $t->fail('addItem() refuses non-sfFeedItem objects');
}
catch(Exception $e)
{
  $t->pass('addItem() refuses non-sfFeedItem objects');
}
try
{
  $feed->addItem(new sfFeedItem());
  $t->pass('addItem() accepts sfFeedItem objects');
}
catch(Exception $e)
{
  $t->fail('addItem() accepts sfFeedItem objects');
}
$t->is(count($feed->getItems()), 1, 'addItem() adds an item to the feed');
$feed->setItems();
$t->is(count($feed->getItems()), 0, 'setItems() with no arguments reinitializes the feed items');
$feed->addItems(array(new sfFeedItem(), new sfFeedItem(), new sfFeedItem()));
$t->is(count($feed->getItems()), 3, 'addItems() adds several items at once');
$feed->setItems();
try
{
  $feed->addItemFromArray(array());
  $t->pass('addItemFromArray() accepts an array');
}
catch(Exception $e)
{
  $t->fail('addItemFromArray() accepts an array');
}
$t->is(count($feed->getItems()), 1, 'addItemFromArray() adds an item to the feed');

$feed_params = array(
  'title' => 'foo', 
  'link' => 'bar', 
  'description' => 'foobar baz',
  'language' => 'fr', 
  'authorName' => 'francois',
  'authorEmail' => 'francois@toto.com',
  'authorLink' => 'http://francois.toto.com',
  'subtitle' => 'this is foo bar',
  'categories' => array('foo', 'bar'),
  'feedUrl' => 'http://www.example.com',
  'encoding' => 'UTF-16',
);

$feed = new sfFeed();
$feed->initialize($feed_params);

$item1_params = array(
  'title' => 'foo', 
  'pubDate' => '1',
);

$item1 = new sfFeedItem();
$item1->initialize($item1_params);

$item2_params = array(
  'title' => 'bar', 
  'pubDate' => '3',
);

$item2 = new sfFeedItem();
$item2->initialize($item2_params);

$item3_params = array(
  'title' => 'baz', 
  'pubDate' => '2',
);

$item3 = new sfFeedItem();
$item3->initialize($item3_params);

$feed->addItems(array($item1, $item2, $item3));
$t->is($feed->getLatestPostDate(), '3', 'getLatestPostDate() returns the latest post date of all feed items');