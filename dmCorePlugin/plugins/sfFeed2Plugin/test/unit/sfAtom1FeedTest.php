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
  'link' => 'http://www.example.com',
);

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

$feed = new sfAtom1Feed();
$feed->initialize($feedParams);
$feedItem = new sfFeedItem();
$feedItem->initialize($itemParams);
$feed->addItem($feedItem);
$feedItem2 = new sfFeedItem();
$feedItem2->initialize($item2Params);
$feed->addItem($feedItem2);
$feedImage = new sfFeedImage();
$feedImage->initialize($image_params);
$feed->setImage($feedImage);

$t = new lime_test(57, new lime_output_color());

$t->diag('toXML() - generated feed');
$feedString = $feed->toXml();
$feedXml = simplexml_load_string($feedString);
$namespaces = $feedXml->getNamespaces();
$t->ok(in_array('http://www.w3.org/2005/Atom', $namespaces), '<feed> is declared with the proper Atom namespace');
$attributes = $feedXml->attributes('http://www.w3.org/XML/1998/namespace');
preg_match('/^<\?xml\s*version="1\.0"\s*encoding="(.*?)".*?\?>$/mi', $feedString, $matches);
$t->is($matches[1], $feed->getEncoding(), 'The encoding is set with the proper feed encoding');
$t->is((string) $attributes['lang'], $feedParams['language'], '<feed> is declared with the proper language');
$t->is((string) $feedXml->title, $feedParams['title'], '<title> contains the proper title');
$t->is((string) $feedXml->link[0]['href'], $feedParams['link'], '<link ref="alternate"> contains the proper link');
$t->is((string) $feedXml->link[1]['href'], $feedParams['feedUrl'], '<link ref="self"> contains the proper link');
$t->is((string) $feedXml->id, $feedParams['link'], '<id> contains the proper id');
$t->is((string) $feedXml->updated, gmstrftime('%Y-%m-%dT%H:%M:%SZ', $item2Params['pubDate']), '<updated> contains the latest publication date of all feed items');
$t->is((string) $feedXml->author->name, $feedParams['authorName'], '<author><name> contains the author name');
$t->is((string) $feedXml->author->email, $feedParams['authorEmail'], '<author><author_email> contains the author email');
$t->is((string) $feedXml->author->uri, $feedParams['authorLink'], '<author><author_link> contains the author link');
$t->is((string) $feedXml->subtitle, $feedParams['subtitle'], '<subtitle> contains the proper subtitle');
$t->is((string) $feedXml->category[0]['term'], $feedParams['categories'][0], '<category> contains the feed category');
$t->is((string) $feedXml->category[1]['term'], $feedParams['categories'][1], '<category> contains the feed category');
$t->is((string) $feedXml->icon, $image_params['favicon'], '<favicon> contains the proper favicon');
$t->is((string) $feedXml->logo, $image_params['image'], '<logo> contains the proper logo');

$t->diag('toXML() - generated feed items');
$t->is((string) $feedXml->entry[0]->title, $itemParams['title'], '<entry><title> contains the item title');
$t->is((string) $feedXml->entry[0]->link['href'], $itemParams['link'], '<entry><link ref="alternate"> contains the proper item link');
$t->is((string) $feedXml->entry[0]->updated, gmstrftime('%Y-%m-%dT%H:%M:%SZ', $itemParams['pubDate']), '<entry><updated> contains the proper item publication date');
$t->is((string) $feedXml->entry[0]->author->name, $itemParams['authorName'], '<entry><author><name> contains the item author name');
$t->is((string) $feedXml->entry[0]->author->email, $itemParams['authorEmail'], '<entry><author><author_email> contains the item author email');
$t->is((string) $feedXml->entry[0]->author->uri, $itemParams['authorLink'], '<entry><author><author_link> contains the item author link');
$t->is((string) $feedXml->entry[0]->id, $itemParams['uniqueId'], '<entry><id> contains the item description');
$t->is((string) $feedXml->entry[0]->summary, $itemParams['description'], '<entry><summary> contains the item description');
$t->is((string) $feedXml->entry[0]->content, $itemParams['content'], '<entry><content> contains the item content');
$t->is((string) $feedXml->entry[0]->category[0]['term'], $itemParams['categories'][0], '<entry><category> contains the item category');
$t->is((string) $feedXml->entry[0]->category[1]['term'], $itemParams['categories'][1], '<entry><category> contains the item category');
$t->is((string) $feedXml->entry[0]->link[1]['href'], $enclosureParams['url'], '<entry><link ref="enclosure"> contains the proper item enclosure url');
$t->is((string) $feedXml->entry[0]->link[1]['length'], $enclosureParams['length'], '<entry><link ref="enclosure"> contains the proper item enclosure length');
$t->is((string) $feedXml->entry[0]->link[1]['type'], $enclosureParams['mimeType'], '<entry><link ref="enclosure"> contains the proper item enclosure mimeType');

$t->diag('asXML() - generated feed');
$feedString = $feed->asXml();
$t->is(sfContext::getInstance()->getResponse()->getContentType(), 'application/atom+xml; charset=UTF-16', 'The response comes with the correct Content-Type');

$t->diag('fromXML() - generated feed');
$generatedFeed = new sfAtom1Feed();
$generatedFeed->fromXml($feedString);
$t->is($generatedFeed->getTitle(), $feed->getTitle(), 'The title property is properly set');
$t->is($generatedFeed->getLink(), $feed->getLink(), 'The link property is properly set');
$t->isnt($generatedFeed->getDescription(), $feed->getDescription(), 'The description property cannot be set from an Atom feed');
$t->is($generatedFeed->getLanguage(), $feed->getLanguage(), 'The language property is properly set');
$t->is($generatedFeed->getAuthorEmail(), $feed->getAuthorEmail(), 'The author email property is properly set');
$t->is($generatedFeed->getAuthorName(), $feed->getAuthorName(), 'The author name property is properly set');
$t->is($generatedFeed->getAuthorLink(), $feed->getAuthorLink(), 'The author link property is properly set');
$t->is($generatedFeed->getSubtitle(), $feed->getSubtitle(), 'The subtitle property is properly set');
$t->is($generatedFeed->getCategories(), $feed->getCategories(), 'The categories property is properly set');
$t->is($generatedFeed->getFeedUrl(), $feed->getFeedUrl(), 'The feedUrl property is properly set');
$t->is($generatedFeed->getEncoding(), $feed->getEncoding(), 'The encoding property is properly set');
$t->is($generatedFeed->getImage()->getFavicon(), $feed->getImage()->getFavicon(), 'The feed favicon property is properly set');
$t->is($generatedFeed->getImage()->getImage(), $feed->getImage()->getImage(), 'The feed logo property is properly set');
$t->is($generatedFeed->getLatestPostDate(), $feed->getLatestPostDate(), 'The latest post date is correct');

$t->diag('fromXML() - generated feed items');
$items = $generatedFeed->getItems();
$generatedItem = $items[0];

$t->is($generatedItem->getTitle(), $feedItem->getTitle(), 'The title property is properly set');
$t->is($generatedItem->getLink(), $feedItem->getLink(), 'The link property is properly set');
$t->is($generatedItem->getDescription(), $feedItem->getDescription(), 'The description property is properly set');
$t->is($generatedItem->getContent(), $feedItem->getContent(), 'The content property is properly set');
$t->is($generatedItem->getAuthorEmail(), $feedItem->getAuthorEmail(), 'The author email property is properly set');
$t->is($generatedItem->getAuthorName(), $feedItem->getAuthorName(), 'The author name property is properly set');
$t->is($generatedItem->getAuthorLink(), $feedItem->getAuthorLink(), 'The author link property is properly set');
$t->is($generatedItem->getPubDate(), $feedItem->getPubdate(), 'The publication date property is properly set');
$t->isnt($generatedItem->getComments(), $feedItem->getComments(), 'The comments property cannot be set from an Atom feed');
$t->is($generatedItem->getUniqueId(), $feedItem->getUniqueId(), 'The unique id property is properly set');
$t->is($generatedItem->getEnclosure()->__toString(), $feedItem->getEnclosure()->__toString(), 'The enclosure property is properly set');
$t->is($generatedItem->getCategories(), $feedItem->getCategories(), 'The categories property is properly set');
