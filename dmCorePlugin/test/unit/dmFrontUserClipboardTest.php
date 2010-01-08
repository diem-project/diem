<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot('front');

$t = new lime_test();

$cb = $helper->get('front_clipboard');

$t->is($cb->getWidget(), null, 'Clipboard has no widget');
$t->is($cb->getZone(), null, 'Clipboard has no zone');

$widget = dmDb::table('DmWidget')->findOne();
$zone = dmDb::table('DmZone')->findOne();

$cb->setWidget($widget);
$cb->setZone($zone);

$t->is($cb->getWidget(), $widget, 'Widget restored');
$t->is($cb->getZone(), $zone, 'Zone restored');