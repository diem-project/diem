<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot('front');

$t = new lime_test();

$pageHelper = $helper->get('page_helper');

$t->is(
  $pageHelper->getOption('widget_css_class_pattern'),
  dmArray::get($helper->get('service_container')->getParameter('page_helper.options'), 'widget_css_class_pattern'),
  'widget_css_class_pattern : '.$pageHelper->getOption('widget_css_class_pattern')
);

$widget = array(
  'id'        => 9999,
  'module'    => 'dmWidgetContent',
  'action'    => 'breadCrumb',
  'value'     => json_encode(array('text' => 'test title', 'tag' => 'h1')),
  'css_class' => 'custom_class'
);

$pageHelper->setOption('widget_css_class_pattern', '');

$expected = array('dm_widget custom_class', 'dm_widget_inner');
$t->is($pageHelper->getWidgetContainerClasses($widget), $expected,'widgetContainerClasses for breadCrumb : '.implode(', ', $expected));

$widget['action'] = 'title';

$expected = array('dm_widget custom_class', 'dm_widget_inner');
$t->is($pageHelper->getWidgetContainerClasses($widget), $expected,'widgetContainerClasses for title : '.implode(', ', $expected));

$pageHelper->setOption('widget_css_class_pattern', '%module%_%action%');

$expected = array('dm_widget content_title custom_class', 'dm_widget_inner');
$t->is($pageHelper->getWidgetContainerClasses($widget), $expected,'widgetContainerClasses for title : '.implode(', ', $expected));

$pageHelper->setOption('widget_css_class_pattern', 'module_%module% action_%action%');

$expected = array('dm_widget module_content action_title custom_class', 'dm_widget_inner');
$t->is($pageHelper->getWidgetContainerClasses($widget), $expected,'widgetContainerClasses for title : '.implode(', ', $expected));