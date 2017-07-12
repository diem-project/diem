<?php

define('SF_ROOT_DIR', realpath(dirname(__FILE__).'/../../../..'));
define('SF_APP',      'frontend');
include(dirname(__FILE__).'/../../../../test/bootstrap/functional.php');

$b = new sfTestBrowser();
$b->initialize();

$feedParams = array(
  'title' => 'foo', 
  'link' => 'http://foobar', 
  'description' => 'foobar baz',
  'language' => 'fr', 
  'authorName' => 'francois',
  'authorEmail' => 'foo@bar.com',
  'authorLink' => 'http://bar.baz',
  'subtitle' => 'hey, foo, this is bar', 
  'categories' => array('foo', 'bar'),
  'feedUrl' => 'http://www.example.com',
  'encoding' => 'UTF-16'
);

$enclosureParams = array(
  'url' => 'foo.com', 
  'length' => '1234', 
  'mimeType' => 'foobarmimetype',
);
$enclosure = new sfFeedEnclosure();
$enclosure->initialize($enclosureParams);

$itemParams = array(
  'title' => 'fooitem', 
  'link' => 'http://www.example.com/item1',
  'description' => 'foobar baz item', 
  'content' => 'this is foo bar baz',  
  'authorName' => 'francois item',
  'authorEmail' => 'fooitem@bar.com',
  'authorLink' => 'http://bar.baz.item',
  'categories' => array('fooitem', 'baritem'),
  'pubDate' => '12345',
  'comments' => 'gee',
  'uniqueId' => '98765',
  'enclosure' => $enclosure,
  'categories' => array('fooitem', 'baritem'),
);

$item2Params = array(
  'title' => 'foobaritem', 
  'pubDate' => '123456',
  'authorEmail' => 'fooitem2@bar.com',
  'link' => 'http://www.example.com/item2',
);

$feed = new sfRss10Feed();
$feed->initialize($feedParams);
$feedItem = new sfFeedItem();
$feedItem->initialize($itemParams);
$feed->addItem($feedItem);
$feedItem2 = new sfFeedItem();
$feedItem2->initialize($item2Params);
$feed->addItem($feedItem2);

$t = new lime_test(35, new lime_output_color());

$t->diag('toXML() - generated feed');
$feedString = $feed->toXml();

// we get rid of namespaces to avoid simpleXML headaches
$feedString = str_replace(
  array('<dc:', '</dc:', '<rdf:', '</rdf:', '<content:', '</content:'), 
  array('<', '</', '<', '</', '<', '</'), 
  $feedString
);

$feedXml = simplexml_load_string($feedString);
$t->is($feedXml->getName(), 'RDF', '<rdf:RDF> is the main tag');
preg_match('/^<\?xml\s*version="1\.0"\s*encoding="(.*?)".*?\?>$/mi', $feedString, $matches);
$t->is($matches[1], $feed->getEncoding(), 'The encoding is set with the proper feed encoding');
$t->is((string) $feedXml->channel[0]->title, $feedParams['title'], '<title> contains the feed title');
$t->is((string) $feedXml->channel[0]->link, $feedParams['link'], '<link> contains the feed link');
$t->is((string) $feedXml->channel[0]->description, $feedParams['description'], '<description> contains the feed description');

$t->diag('toXML() - generated feed items');
$t->is((string) $feedXml->item[0]->title, $itemParams['title'], '<item><title> contains the item title');
$t->is((string) $feedXml->item[0]->link, $itemParams['link'], '<item><link> contains the proper item link');
$t->is((string) $feedXml->item[0]->description, $itemParams['description'], '<item><description> contains the item description');
$t->is((string) $feedXml->item[0]->encoded, $itemParams['content'], '<item><content:encoded> contains the item content');
$t->is((string) $feedXml->item[0]->date, gmstrftime('%Y-%m-%dT%H:%M:%SZ', $itemParams['pubDate']), '<item><dc:date> contains the proper item publication date');
$t->is((string) $feedXml->item[0]->creator, $itemParams['authorName'], '<item><dc:creator> contains the proper item author name');

$t->diag('asXML() - generated feed');
$feedString = $feed->asXml();
$t->is(sfContext::getInstance()->getResponse()->getContentType(), 'application/rss+xml; charset=UTF-16', 'The reponse comes with the correct Content-Type');

$t->diag('fromXML() - generated feed');
$generatedFeed = new sfRss10Feed();
$generatedFeed->fromXml($feedString);
$t->is($generatedFeed->getTitle(), $feed->getTitle(), 'The title property is properly set');
$t->is($generatedFeed->getLink(), $feed->getLink(), 'The link property is properly set');
$t->is($generatedFeed->getDescription(), $feed->getDescription(), 'The description property is properly set');
$t->isnt($generatedFeed->getLanguage(), $feed->getLanguage(), 'The language property cannot be set from a RSS 1.0 feed');
$t->isnt($generatedFeed->getAuthorEmail(), $feed->getAuthorEmail(), 'The author email property cannot be set from a RSS 1.0 feed');
$t->isnt($generatedFeed->getAuthorName(), $feed->getAuthorName(), 'The author name property cannot be set from a RSS 1.0 feed');
$t->isnt($generatedFeed->getAuthorLink(), $feed->getAuthorLink(), 'The author link property cannot be set from a RSS 1.0 feed');
$t->isnt($generatedFeed->getSubtitle(), $feed->getSubtitle(), 'The subtitle property cannot be set from a RSS 1.0 feed');
$t->isnt($generatedFeed->getCategories(), $feed->getCategories(), 'The categories property cannot be set from a RSS 1.0 feed');
$t->isnt($generatedFeed->getFeedUrl(), $feed->getFeedUrl(), 'The feedUrl property cannot be set from a RSS 1.0 feed');
$t->is($generatedFeed->getEncoding(), $feed->getEncoding(), 'The encoding property is properly set');

$t->diag('fromXML() - generated feed items');
$items = $generatedFeed->getItems();
$generatedItem = $items[0];
$t->is($generatedItem->getTitle(), $feedItem->getTitle(), 'The title property is properly set');
$t->is($generatedItem->getLink(), $feedItem->getLink(), 'The link property is properly set');
$t->is($generatedItem->getDescription(), $feedItem->getDescription(), 'The description property is properly set');
$t->is($generatedItem->getContent(), $feedItem->getContent(), 'The content property is properly set');
$t->isnt($generatedItem->getAuthorEmail(), $feedItem->getAuthorEmail(), 'The author email property cannot be set from a RSS 1.0 feed');
$t->is($generatedItem->getAuthorName(), $feedItem->getAuthorName(), 'The author name property is properly set');
$t->isnt($generatedItem->getAuthorLink(), $feedItem->getAuthorLink(), 'The author link property cannot be set from a RSS 1.0 feed');
$t->is($generatedItem->getPubDate(), $feedItem->getPubdate(), 'The publication date property is properly set');
$t->isnt($generatedItem->getComments(), $feedItem->getComments(), 'The comments property cannot be set from a RSS 1.0 feed');
$t->isnt($generatedItem->getUniqueId(), $feedItem->getUniqueId(), 'The unique id property cannot be set from a RSS 1.0 feed');
$t->is($generatedItem->getEnclosure(), '', 'The enclosure property cannot be set from a RSS 1.0 feed');
$t->isnt($generatedItem->getCategories(), $feedItem->getCategories(), 'The categories property cannot be set from a RSS 1.0 feed');


