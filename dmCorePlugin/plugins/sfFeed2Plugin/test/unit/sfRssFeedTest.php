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

$feed = new sfRssFeed();
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

$t = new lime_test(62, new lime_output_color());

$t->diag('toXML() - generated feed');
$feedString = $feed->toXml();

$feedXml = simplexml_load_string($feedString);
$t->is($feedXml->getName(), 'rss', '<rss> is the main tag');
preg_match('/^<\?xml\s*version="1\.0"\s*encoding="(.*?)".*?\?>$/mi', $feedString, $matches);
$t->is($matches[1], $feed->getEncoding(), 'The encoding is set with the proper feed encoding');
$t->is((string) $feedXml->channel[0]->title, $feedParams['title'], '<title> contains the feed title');
$t->is((string) $feedXml->channel[0]->link, $feedParams['link'], '<link> contains the feed link');
$t->is((string) $feedXml->channel[0]->description, $feedParams['description'], '<description> contains the feed description');
$t->is((string) $feedXml->channel[0]->language, $feedParams['language'], '<language> contains the feed language');
$t->is((string) $feedXml->channel[0]->managingEditor, $feedParams['authorEmail'].' ('.$feedParams['authorName'].')', '<managingEditor> contains the author email and name');
$t->is((string) $feedXml->channel[0]->pubDate, date(DATE_RFC822, $item2Params['pubDate']), '<pubDate> contains the latest publication date of all feed items');
$t->is_deeply(array((string) $feedXml->channel[0]->category[0], (string) $feedXml->channel[0]->category[1]), $feedParams['categories'], '<category> contains the correct categories');
$t->is((string) $feedXml->channel[0]->image->url, $image_params['image'], '<image><url> contains the proper image');
$t->is((string) $feedXml->channel[0]->image->width, $image_params['imageX'], '<image><width> contains the proper image x');
$t->is((string) $feedXml->channel[0]->image->height, $image_params['imageY'], '<image><height> contains the proper image y');
$t->is((string) $feedXml->channel[0]->image->link, $image_params['link'], '<image><link> contains the proper image link');
$t->is((string) $feedXml->channel[0]->image->title, $image_params['title'], '<image><title> contains the proper image title');

$t->diag('toXML() - generated feed items');
$t->is((string) $feedXml->channel[0]->item[0]->title, $itemParams['title'], '<item><title> contains the item title');
$t->is((string) $feedXml->channel[0]->item[0]->link, $itemParams['link'], '<item><link> contains the proper item link');
$t->is((string) $feedXml->channel[0]->item[0]->pubDate, date(DATE_RFC822, $itemParams['pubDate']), '<item><pubDate> contains the proper item publication date');
preg_match('/(.*?)\s*\((.*?)\)/', (string) $feedXml->channel[0]->item[0]->author, $matches);
$t->is_deeply(array($matches[1], $matches[2]), array($itemParams['authorEmail'], $itemParams['authorName']), '<entry><author> contains the item author with the pattern email (name)');
$t->is((string) $feedXml->channel[0]->item[0]->guid, $itemParams['uniqueId'], '<item><guid> contains the item description');
$t->is((string) $feedXml->channel[0]->item[0]->description, $itemParams['description'], '<item><description> contains the item description');
$content = $feedXml->channel[0]->item[0]->children("http://purl.org/rss/1.0/modules/content/");
$t->is((string) $content->encoded, $itemParams['content'], '<item><content:encoded> contains the item content');
$t->is((string) $feedXml->channel[0]->item[0]->category[0], $itemParams['categories'][0], '<item><category> contains the item category');
$t->is((string) $feedXml->channel[0]->item[0]->category[1], $itemParams['categories'][1], '<item><category> contains the item category');
$generatedEnclosure = array(
  'url'    => (string) $feedXml->channel[0]->item[0]->enclosure['url'],
  'length' => (string) $feedXml->channel[0]->item[0]->enclosure['length'],
  'mimeType'   => (string) $feedXml->channel[0]->item[0]->enclosure['type']
);
$t->is_deeply($generatedEnclosure, $enclosureParams, '<entry><enclosure> contains the proper item enclosure');

$t->diag('asXML() - generated feed');
$feedString = $feed->asXml();
$t->is(sfContext::getInstance()->getResponse()->getContentType(), 'application/rss+xml; charset=UTF-16', 'The reponse comes with the correct Content-Type');

$t->diag('fromXML() - generated feed');
$generatedFeed = new sfRssFeed();
$generatedFeed->fromXml($feedString);
$t->is($generatedFeed->getTitle(), $feed->getTitle(), 'The title property is properly set');
$t->is($generatedFeed->getLink(), $feed->getLink(), 'The link property is properly set');
$t->is($generatedFeed->getDescription(), $feed->getDescription(), 'The description property is properly set');
$t->is($generatedFeed->getLanguage(), $feed->getLanguage(), 'The language property is properly set');
$t->is($generatedFeed->getAuthorEmail(), $feed->getAuthorEmail(), 'The author email property is properly set');
$t->is($generatedFeed->getAuthorName(), $feed->getAuthorName(), 'The author name property is properly set');
$t->isnt($generatedFeed->getAuthorLink(), $feed->getAuthorLink(), 'The author link property cannot be set from a RSS feed');
$t->isnt($generatedFeed->getSubtitle(), $feed->getSubtitle(), 'The subtitle property cannot be set from a RSS feed');
$t->is($generatedFeed->getCategories(), $feed->getCategories(), 'The categories property is properly set');
$t->isnt($generatedFeed->getFeedUrl(), $feed->getFeedUrl(), 'The feedUrl property cannot be set from a RSS feed');
$t->is($generatedFeed->getEncoding(), $feed->getEncoding(), 'The encoding property is properly set');
$t->is($generatedFeed->getImage()->getImage(), $feed->getImage()->getImage(), 'The feed image url is correctly set');
$t->is($generatedFeed->getImage()->getImageX(), $feed->getImage()->getImageX(), 'The feed image x is correctly set');
$t->is($generatedFeed->getImage()->getImageY(), $feed->getImage()->getImageY(), 'The feed image y is correctly set');
$t->is($generatedFeed->getImage()->getLink(), $feed->getImage()->getLink(), 'The feed image link is correctly set');
$t->is($generatedFeed->getImage()->getTitle(), $feed->getImage()->getTitle(), 'The feed image title is correctly set');

$t->diag('fromXML() - generated feed items');
$items = $generatedFeed->getItems();
$generatedItem = $items[0];
$t->is($generatedItem->getTitle(), $feedItem->getTitle(), 'The title property is properly set');
$t->is($generatedItem->getLink(), $feedItem->getLink(), 'The link property is properly set');
$t->is($generatedItem->getDescription(), $feedItem->getDescription(), 'The description property is properly set');
$t->is($generatedItem->getContent(), $feedItem->getContent(), 'The content property is properly set');
$t->is($generatedItem->getAuthorEmail(), $feedItem->getAuthorEmail(), 'The author email property is properly set');
$t->is($generatedItem->getAuthorName(), $feedItem->getAuthorName(), 'The author name property is properly set');
$t->isnt($generatedItem->getAuthorLink(), $feedItem->getAuthorLink(), 'The author link property cannot be set from a RSS feed');
$t->is($generatedItem->getPubDate(), $feedItem->getPubdate(), 'The publication date property is properly set');
$t->is($generatedItem->getComments(), $feedItem->getComments(), 'The comments property is properly set');
$t->is($generatedItem->getUniqueId(), $feedItem->getUniqueId(), 'The unique id property is properly set');
$t->is($generatedItem->getEnclosure()->__toString(), $feedItem->getEnclosure()->__toString(), 'The enclosure property is properly set');
$t->is($generatedItem->getCategories(), $feedItem->getCategories(), 'The categories property is properly set');
$generatedItem = $items[1];
$t->is($generatedItem->getAuthorEmail(), $feedItem2->getAuthorEmail(), 'The author email property is properly set, even if there is no email address');


$t->diag('RSS 0.91');

$feed = new sfRssFeed();
$feed->setVersion('0.91');
$feed->initialize($feedParams);
$feedItem = new sfFeedItem();
$feedItem->initialize($itemParams);
$feed->addItem($feedItem);
$feedItem2 = new sfFeedItem();
$feedItem2->initialize($item2Params);
$feed->addItem($feedItem2);

$feedString = $feed->asXml();
$generatedFeed = new sfRssFeed();
$generatedFeed->fromXml($feedString);

$t->isnt($generatedFeed->getCategories(), $feed->getCategories(), '<category> doesn\'t exist in a RSS0.91 feed');
$items = $generatedFeed->getItems();
$generatedItem = $items[0];
$t->isnt($generatedItem->getAuthorEmail(), $feedItem->getAuthorEmail(), '<item><author> doesn\'t exist in a RSS0.91 feed');
$t->isnt($generatedItem->getAuthorName(), $feedItem->getAuthorName(), '<item><author> doesn\'t exist in a RSS0.91 feed');
$t->isnt($generatedItem->getPubDate(), $feedItem->getPubdate(), '<item><pubDate> doesn\'t exist in a RSS0.91 feed');
$t->isnt($generatedItem->getComments(), $feedItem->getComments(), '<item><comments> doesn\'t exist in a RSS0.91 feed');
$t->isnt($generatedItem->getUniqueId(), $feedItem->getUniqueId(), '<item><guid> doesn\'t exist in a RSS0.91 feed');
$t->is($generatedItem->getEnclosure(), '', '<item><enclosure> doesn\'t exist in a RSS0.91 feed');
$t->isnt($generatedItem->getCategories(), $feedItem->getCategories(), '<item><category> doesn\'t exist in a RSS0.91 feed');
