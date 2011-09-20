<?php

require_once(realpath(dirname(__FILE__).'/../../..').'/unit/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot('front');

$t = new lime_test(39);

$wtm = $helper->get('widget_type_manager');

$widgetType = $wtm->getWidgetType('dmWidgetNavigation', 'menu');

$formClass = $widgetType->getOption('form_class');

$page1 = dmDb::table('DmPage')->findOneByModuleAndAction('main', 'page1');

$t->comment('Create a menu widget');

$widget = dmDb::create('DmWidget', array(
  'module' => $widgetType->getModule(),
  'action' => $widgetType->getAction(),
  'value'  => '[]',
  'dm_zone_id' => $page1->PageView->Area->Zones[0]
));

$t->comment('Create a '.$formClass.' instance');

$form = new $formClass($widget);
$form->removeCsrfProtection();

$html = $form->render();
$t->like($html, '_^<form\s(.|\n)*</form>$_', 'Successfully obtained and rendered a '.$formClass.' instance');

$t->is(count($form->getStylesheets()), 2, 'This widget form requires 2 additional stylesheets');
$t->is(count($form->getJavascripts()), 3, 'This widget form requires 3 additional javascripts');

$t->comment('Submit an empty form');

$form->bind(array(), array());
$t->is($form->isValid(), true, 'The form is  valid');

$t->comment('Save the widget');

$form->updateWidget()->save();

$t->ok($widget->exists(), 'Widget has been saved');
$expected = array(
  'ulClass' => '',  'menuName' => '',  'liClass' => '',  'items' => array()
);
$t->is_deeply($widget->values, $expected, 'Widget values are correct');

$t->comment('Recreate the form from the saved widget');

$form = new $formClass($widget);
$form->removeCsrfProtection();

$t->is($form->getDefault('link'), array(), 'The form default text is correct');
$t->is($form->getDefault('text'), array(), 'The form default link is correct');

$t->comment('Submit form without additional data');
$form->bind(array(), array());
$t->is($form->isValid(), true, 'The form is valid');
if (!$form->isValid())
{
  $t->comment($form->getErrorSchema()->getMessage());
}

$t->comment('Add an item');
$form->bind(array(
  'link' => array('page:1'),
  'text' => array('Home'),
  'depth' => 0,
  'cssClass' => 'test css_class',
), array());
$t->is($form->isValid(), true, 'The form is valid');
if (!$form->isValid())
{
  $t->comment($form->getErrorSchema()->getMessage());
}

$t->comment('Save the widget');

$form->updateWidget()->save();

$t->ok($widget->exists(), 'Widget has been saved');
$t->is_deeply($widget->values, array(
  'ulClass' => '',  'menuName' => '',  'liClass' => '', 'items' => array(array('link' => 'page:1', 'text' => 'Home', 'secure' => 0, 'nofollow' => 0, 'depth' => null, 'target' => null))
), 'Widget values are correct');

$t->comment('Recreate the form from the saved widget');

$form = new $formClass($widget);
$form->removeCsrfProtection();

$t->is($form->getDefault('items'), array(array('link' => 'page:1', 'text' => 'Home', 'depth' => 0, 'secure' => 0, 'nofollow' => 0, 'depth' => null, 'target' => null)), 'The form default items are correct');

$t->comment('Now display the widget');

$widgetArray = $widget->toArrayWithMappedValue();

$expected = array(
  'action' => 'menu',
  'css_class' => 'test css_class',
  'dm_zone_id' => $widget->Zone->id,
  'id' => $widget->id,
  'module' => 'dmWidgetNavigation',
  'position' => $widget->position,
  'value' => json_encode(array(
    'ulClass' => '',  'menuName' => '',  'liClass' => '', 'items' => array(array('link' => 'page:1', 'text' => 'Home', 'secure' => 0, 'nofollow' => 0, 'depth' => null, 'target' => null))
  )),
  'updated_at' => $widget->updatedAt
);

$t->is_deeply($helper->ksort($widgetArray), $helper->ksort($expected), 'Widget array with mapped value is correct');

$helper->get('service_container')->setParameter('widget_renderer.widget', $widgetArray);

$widgetRenderer = $helper->get('service_container')->getService('widget_renderer');

// gather widget assets to load asynchronously

$t->is($widgetRenderer->getStylesheets(), array(), 'This widget view requires no additional stylesheet');
$t->is($widgetRenderer->getJavascripts(), array(), 'This widget view requires no additional javascript');

$t->ok($widgetRenderer->getHtml(), 'The widget has been rendered');

$widgetView = $widgetRenderer->getWidgetView();

$t->isa_ok($widgetView, $widgetType->getOption('view_class'), 'The widget view is a '.$widgetType->getOption('view_class'));

$t->ok(!$widgetView->isRequiredVar('mediaId'), 'mediaId is not a view required var');
$t->ok($widgetView->isRequiredVar('items'), 'items is a view required var');

$expected = $helper->get('menu')
->ulClass('')
->addChild('0-home', 'page:1')->label('Home')->end()
->render();
$t->is($widgetView->render(), $expected, 'render : '.$expected);

$t->is($widgetView->renderForIndex(), '', 'render for index is empty');

$t->comment('Add a page, a non-link, an external link, a mailto, ulClass, menuName and liClass');

$form->bind(array(
  'link' => array('page:1', 'page:'.$page1->id, '', 'http://jquery.com', 'mailto:mail@a.com'),
  'text' => array('Home', 'Page 1', 'nolink', 'jquery', 'mail'),
  'secure' => array(0, 0, 0, 0, 0),
  'nofollow' => array(0, 0, 0, 0, 1),
  'depth' => array(0, 0, 0, 0, 0),
  'menuName' => 'my_menu_name',
  'ulClass' => 'my_ul_class',
  'liClass' => 'my_li_class',
  'cssClass' => 'test css_class',
), array());
$t->is($form->isValid(), true, 'The form is valid');
if (!$form->isValid())
{
  $t->comment($form->getErrorSchema()->getMessage());
}

$t->comment('Save the widget');

$form->updateWidget()->save();

$t->ok($widget->exists(), 'Widget has been saved');
$t->is_deeply($helper->ksort($widget->values), $helper->ksort(array(
  'ulClass' => 'my_ul_class',  'menuName' => 'my_menu_name',  'liClass' => 'my_li_class', 'items' => array(
    array('link' => 'page:1', 'text' => 'Home', 'secure' => 0, 'nofollow' => 0, 'depth' => 0, 'target' => null),
    array('link' => 'page:'.$page1->id, 'text' => 'Page 1', 'secure' => 0, 'nofollow' => 0, 'depth' => 0, 'target' => null),
    array('link' => '', 'text' => 'nolink', 'depth' => 0, 'secure' => 0, 'nofollow' => 0, 'depth' => 0, 'target' => null),
    array('link' => 'http://jquery.com', 'text' => 'jquery', 'secure' => 0, 'nofollow' => 0, 'depth' => 0, 'target' => null),
    array('link' => 'mailto:mail@a.com', 'text' => 'mail', 'secure' => 0, 'nofollow' => 1, 'depth' => 0, 'target' => null)
  ))
), 'Widget values are correct');

$t->comment('Recreate the form from the saved widget');

$form = new $formClass($widget);
$form->removeCsrfProtection();

$t->is($form->getDefault('items'), array(
  array('link' => 'page:1', 'text' => 'Home', 'secure' => 0, 'nofollow' => 0, 'depth' => 0, 'target' => null),
  array('link' => 'page:'.$page1->id, 'text' => 'Page 1', 'secure' => 0, 'nofollow' => 0, 'depth' => 0, 'target' => null),
  array('link' => '', 'text' => 'nolink', 'secure' => 0, 'nofollow' => 0, 'depth' => 0, 'target' => null),
  array('link' => 'http://jquery.com', 'text' => 'jquery', 'secure' => 0, 'nofollow' => 0, 'depth' => 0, 'target' => null),
  array('link' => 'mailto:mail@a.com', 'text' => 'mail', 'secure' => 0, 'nofollow' => 1, 'depth' => 0, 'target' => null)
), 'The form default items are correct');

$t->comment('Now display the widget');

$widgetArray = $widget->toArrayWithMappedValue();

$expected = array(
  'action' => 'menu',
  'css_class' => 'test css_class',
  'dm_zone_id' => $widget->Zone->id,
  'id' => $widget->id,
  'module' => 'dmWidgetNavigation',
  'position' => $widget->position,
  'value' => json_encode(array(
    'menuName' => 'my_menu_name', 'ulClass' => 'my_ul_class', 'liClass' => 'my_li_class', 'items' => array(
      array('link' => 'page:1', 'text' => 'Home', 'secure' => 0, 'nofollow' => 0, 'depth' => 0, 'target' => null),
      array('link' => 'page:'.$page1->id, 'text' => 'Page 1', 'secure' => 0, 'nofollow' => 0, 'depth' => 0, 'target' => null),
      array('link' => '', 'text' => 'nolink', 'secure' => 0, 'nofollow' => 0, 'depth' => 0, 'target' => null),
      array('link' => 'http://jquery.com', 'text' => 'jquery', 'secure' => 0, 'nofollow' => 0, 'depth' => 0, 'target' => null),
      array('link' => 'mailto:mail@a.com', 'text' => 'mail', 'secure' => 0, 'nofollow' => 1, 'depth' => 0, 'target' => null)
    )
  )),
  'updated_at' => $widget->updatedAt
);

$t->is_deeply($helper->ksort($widgetArray), $helper->ksort($expected), 'Widget array with mapped value is correct');

$helper->get('service_container')->setParameter('widget_renderer.widget', $widgetArray);

$widgetRenderer = $helper->get('service_container')->getService('widget_renderer');

$t->ok($widgetRenderer->getHtml(), 'The widget has been rendered');

$widgetView = $widgetRenderer->getWidgetView();

$expectedMenu = $helper->get('menu')
->ulClass('my_ul_class')
->addChild('0-home', 'page:1')->label('Home')->liClass('my_li_class')->end()
->addChild('1-page-1', 'page:'.$page1->id)->label('Page 1')->liClass('my_li_class')->end()
->addChild('2-nolink', '')->label('nolink')->liClass('my_li_class')->end()
->addChild('3-jquery', 'http://jquery.com')->label('jquery')->liClass('my_li_class')->end();

$t->comment('Authenticate the user');
$helper->get('user')->setAuthenticated(true);

$expectedMenu->addChild('4-mail', 'mailto:mail@a.com')->label('mail')->liClass('my_li_class')->end();
$expectedMenu['4-mail']->getLink()->set('rel', 'nofollow');

$t->is($widgetView->render(), $expectedMenu->render(), 'render : '.$expectedMenu->render());

$t->like($widgetView->render(), '|<li class="my_li_class">nolink</li>|', 'Menu contains a non link');

$t->like($widgetView->render(), '|<li class="my_li_class"><a class="link" href="http://jquery.com">jquery</a></li>|', 'Menu contains a external link');

$t->like($widgetView->render(), '|<li class="last my_li_class"><a class="link" href="mailto:mail@a.com" rel="nofollow">mail</a></li>|', 'Menu contains a mailto:');

$t->is($widgetView->renderForIndex(), '', 'render for index is empty');

$t->comment('Set depth=1');

$form->bind(array(
  'link' => array('page:1', 'page:'.$page1->id),
  'text' => array('Home', 'Page 1'),
  'secure' => 0,
  'nofollow' => 0,
  'depth' => array(0, 1),
	'target' => null,
  'menuName' => 'my_menu_name',
  'ulClass' => 'my_ul_class',
  'liClass' => 'my_li_class',
  'cssClass' => 'test css_class',
), array());
$t->is($form->isValid(), true, 'The form is valid');
if (!$form->isValid())
{
  $t->comment($form->getErrorSchema()->getMessage());
}

$t->comment('Save the widget');

$form->updateWidget()->save();

$t->ok($widget->exists(), 'Widget has been saved');
$t->is_deeply($helper->ksort($widget->values), $helper->ksort(array(
  'ulClass' => 'my_ul_class',  'menuName' => 'my_menu_name',  'liClass' => 'my_li_class', 'items' => array(
    array('link' => 'page:1', 'text' => 'Home', 'secure' => 0, 'nofollow' => 0, 'depth' => 0, 'target' => null),
    array('link' => 'page:'.$page1->id, 'text' => 'Page 1', 'secure' => 0, 'nofollow' => 0, 'depth' => 1, 'target' => null)
  ))
), 'Widget values are correct');

$t->comment('Now display the widget');

$widgetArray = $widget->toArrayWithMappedValue();

$expected = array(
  'action' => 'menu',
  'css_class' => 'test css_class',
  'dm_zone_id' => $widget->Zone->id,
  'id' => $widget->id,
  'module' => 'dmWidgetNavigation',
  'position' => $widget->position,
  'value' => json_encode(array(
    'menuName' => 'my_menu_name', 'ulClass' => 'my_ul_class', 'liClass' => 'my_li_class', 'items' => array(
      array('link' => 'page:1', 'text' => 'Home', 'secure' => 0, 'nofollow' => 0, 'depth' => 0, 'target' => null),
      array('link' => 'page:'.$page1->id, 'text' => 'Page 1', 'secure' => 0, 'nofollow' => 0, 'depth' => 1, 'target' => null)
    )
  )),
  'updated_at' => $widget->updatedAt
);

$t->is_deeply($helper->ksort($widgetArray), $helper->ksort($expected), 'Widget array with mapped value is correct');

$helper->get('service_container')->setParameter('widget_renderer.widget', $widgetArray);

$widgetRenderer = $helper->get('service_container')->getService('widget_renderer');

$t->ok($widgetRenderer->getHtml(), 'The widget has been rendered');

$widgetView = $widgetRenderer->getWidgetView();

$expected = $helper->get('menu')
->ulClass('my_ul_class')
->addChild('0-home', 'page:1')->label('Home')->liClass('my_li_class')->end()
->addChild('1-page-1', 'page:'.$page1->id)->label('Page 1')->liClass('my_li_class')->addRecursiveChildren(1)->end()
->render();
$t->is($widgetView->render(), $expected, 'render : '.$expected);