<?php

include(dirname(__FILE__).'/../../../../test/bootstrap/unit.php');
require_once(dirname(__FILE__).'/../../lib/sfFeedEnclosure.class.php');

$t = new lime_test(4, new lime_output_color());

$enclosureParams = array(
  'url' => 'foo.com', 
  'length' => '1234', 
  'mimeType' => 'foobarmimetype',
);

$enclosure = new sfFeedEnclosure();
$t->isa_ok($enclosure->initialize($enclosureParams), 'sfFeedEnclosure', 'initialize() returns the current feed enclosure object');
$t->is($enclosure->getUrl(), $enclosureParams['url'], 'getUrl() gets the feed enclosure url');
$t->is($enclosure->getLength(), $enclosureParams['length'], 'getLength() gets the feed enclosure length');
$t->is($enclosure->getMimeType(), $enclosureParams['mimeType'], 'getMimeType() gets the feed enclosure mimetype');
