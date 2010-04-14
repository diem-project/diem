<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot();

$t = new lime_test(19);
$browser = $helper->get('browser');

$namorokaUbuntu = 'Mozilla/5.0 (X11; U; Linux x86_64; en-US; rv:1.9.2pre) Gecko/20100116 Ubuntu/9.10 (karmic) Namoroka/3.6pre';
$namorokaMac = 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.6; en-US; rv:1.9.2) Gecko/20100105 Firefox/3.6';
$chromeMac = 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_2; en-US) AppleWebKit/532.5 (KHTML, like Gecko) Chrome/4.0.249.49 Safari/532.5';
$safariMac = 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_2; fr-fr) AppleWebKit/531.21.8 (KHTML, like Gecko) Version/4.0.4 Safari/531.21.10';
$opera9Windows = 'Opera/9.61 (Windows NT 6.0; U; en) Presto/2.1.1';
$opera10Windows = 'Opera/9.80 (Windows NT 5.1; U; en) Presto/2.2.15 Version/10.10';
$googleBot = 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)';
$msnBot = 'msnbot/2.0b (+http://search.msn.com/msnbot.htm)';
$firefoxLinux = 'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.0.17) Gecko/2010010604 Linux Mint/7 (Gloria) Firefox/3.0.17';
$firefoxWindows = 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-GB; rv:1.9.1.7) Gecko/20091221 Firefox/3.5.7 GTB6 (.NET CLR 3.5.30729)';
$firefoxOsx = 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.6; en-US; rv:1.9.1.8) Gecko/20100202 Firefox/3.5.8';
//$firefoxWindowsSp2 = 'Gecko 2009122116Mozilla/5.0 (Windows; U; Windows NT 6.0; de; rv:1.9.0.17) Gecko/2009122116 Firefox[xSP_2:077784879bbf239604c69a247f6786a0_220] 967907703 (.NET CLR 3.5.30729)';
$chromeLinux = 'Mozilla/5.0 (X11; U; Linux i686; en-US) AppleWebKit/532.5 (KHTML, like Gecko) Chrome/4.0.249.43 Safari/532.5';
$speedySpider = 'Speedy Spider (http://www.entireweb.com/about/search_tech/speedy_spider/)';
$minefieldMac = 'Gecko 20100113Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.5; en-US; rv:1.9.3a1pre) Gecko/20100113 Minefield/3.7a1pre';
$ie7Windows = 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; Trident/4.0; GTB6; SLCC1; .NET CLR 2.0.50727; OfficeLiveConnector.1.3; OfficeLivePatch.0.0; .NET CLR 3.5.30729; InfoPath.2; .NET CLR 3.0.30729; MSOffice 12)';
$ie6Windows = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0; DigExt)';
$feedFetcherGoogle = 'Feedfetcher-Google; (+http://www.google.com/feedfetcher.html; 2 subscribers; feed-id=6924676383167400434)';
$iphone = 'Mozilla/5.0 (iPhone; U; CPU iPhone OS 3_1_2 like Mac OS X; de-de) AppleWebKit/528.18 (KHTML, like Gecko) Mobile/7D11';
$yahooBot = 'Mozilla/5.0 (compatible; Yahoo! Slurp; http://help.yahoo.com/help/us/ysearch/slurp)';

$tests = array(
  $namorokaUbuntu => array('name' => 'firefox', 'version' => '3.6', 'is_unknown' => false),
  $namorokaMac => array('name' => 'firefox', 'version' => '3.6', 'is_unknown' => false),
  $chromeMac => array('name' => 'chrome', 'version' => '4.0', 'is_unknown' => false),
  $safariMac => array('name' => 'safari', 'version' => '4.0', 'is_unknown' => false),
  $googleBot => array('name' => 'googlebot', 'version' => '2.1', 'is_unknown' => false),
  $msnBot => array('name' => 'msnbot', 'version' => '2.0', 'is_unknown' => false),
  $yahooBot => array('name' => 'yahoobot', 'version' => null, 'is_unknown' => false),
  $opera9Windows => array('name' => 'opera', 'version' => '9.61', 'is_unknown' => false),
  $opera10Windows => array('name' => 'opera', 'version' => '10.10', 'is_unknown' => false),
  $firefoxLinux => array('name' => 'firefox', 'version' => '3.0', 'is_unknown' => false),
  $firefoxWindows => array('name' => 'firefox', 'version' => '3.5', 'is_unknown' => false),
  $firefoxOsx => array('name' => 'firefox', 'version' => '3.5', 'is_unknown' => false),
//  $firefoxWindowsSp2 => array('name' => 'firefox', 'version' => '3.0', 'is_unknown' => false),
  $chromeLinux => array('name' => 'chrome', 'version' => '4.0', 'is_unknown' => false),
  $speedySpider => array('name' => null, 'version' => null, 'is_unknown' => true),
  $minefieldMac => array('name' => 'firefox', 'version' => '3.7', 'is_unknown' => false),
  $ie7Windows => array('name' => 'msie', 'version' => '7.0', 'is_unknown' => false),
  $ie6Windows => array('name' => 'msie', 'version' => '6.0', 'is_unknown' => false),
  $feedFetcherGoogle => array('name' => null, 'version' => null, 'is_unknown' => true),
  $iphone => array('name' => 'iphone', 'version' => '528.18', 'is_unknown' => false),
);

$parser = new phpUserAgentStringParser();

foreach($tests as $userAgent => $description)
{
  $browser->configureFromUserAgentString($userAgent, $parser);
  $t->is_deeply($browser->describe(), $description, $userAgent.' -> '.implode(', ', $description));
}