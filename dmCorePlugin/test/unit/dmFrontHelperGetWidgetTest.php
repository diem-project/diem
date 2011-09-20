<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot('front');

$t = new lime_test();

dm::loadHelpers('DmFront');

$title = dm_get_widget('dmWidgetContent', 'title', array(
  'tag'  => 'h1',
  'text' => 'The title text'
));
$expected = '<div class="dm_widget content_title"><div class="dm_widget_inner"><h1>The title text</h1></div></div>';

$t->is($title, $expected, 'rendered title H1');

$title = dm_get_widget('dmWidgetContent', 'title', array(
  'tag'  => 'h2',
  'text' => 'The title text',
  'css_class' => 'custom_class'
));
$expected = '<div class="dm_widget content_title custom_class"><div class="dm_widget_inner"><h2>The title text</h2></div></div>';

$t->is($title, $expected, 'rendered title H2 with CSS class');

$title = dm_get_widget('main', 'header', array(
  'name' => 'Thibault',
  'css_class' => 'custom_class'
));
$expected = '<div class="dm_widget main_header custom_class"><div class="dm_widget_inner">name: Thibault</div></div>';

$t->is($title, $expected, 'rendered main header with component param and CSS class');