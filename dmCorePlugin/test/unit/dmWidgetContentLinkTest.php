<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot('front');

$t = new lime_test(26);

$wtm = $helper->get('widget_type_manager');

$widgetType = $wtm->getWidgetType('dmWidgetContent', 'link');

$formClass = $widgetType->getOption('form_class');

$t->comment('Create a test page');

$testPage = dmDb::create('DmPage', array(
  'module'  => dmString::random(),
  'action'  => dmString::random(),
  'name'    => dmString::random(),
  'slug'    => dmString::random()
));

$testPage->Node->insertAsFirstChildOf(dmDb::table('DmPage')->getTree()->fetchRoot());

$t->comment('Create a test widget');

$widget = dmDb::create('DmWidget', array(
  'module' => $widgetType->getModule(),
  'action' => $widgetType->getAction(),
  'value'  => '[]',
  'dm_zone_id' => $testPage->PageView->Area->Zones[0]
));

$t->comment('Create a '.$formClass.' instance');

dmDb::table('DmMediaFolder')->checkRoot();
$t->comment('Create a test image media');

$mediaFileName = 'test_'.dmString::random().'.jpg';
copy(
  dmOs::join(sfConfig::get('dm_core_dir'), 'data/image/defaultMedia.jpg'),
  dmOs::join(sfConfig::get('sf_upload_dir'), $mediaFileName)
);
$media = dmDb::create('DmMedia', array(
  'file' => $mediaFileName,
  'dm_media_folder_id' => dmDb::table('DmMediaFolder')->checkRoot()->id
))->saveGet();

$t->ok($media->exists(), 'A test media has been created');

$form = new $formClass($widget);
$form->removeCsrfProtection();

$html = $form->render();
$t->like($html, '_^<form\s(.|\n)*</form>$_', 'Successfully obtained and rendered a '.$formClass.' instance');

$t->is($form->getStylesheets(), array(), 'This widget form requires no additional stylesheet');
$t->is($form->getJavascripts(), array(), 'This widget form requires no additional javascript');

$t->comment('Submit an empty form');

$form->bind(array(), array());
$t->is($form->isValid(), false, 'The form is not valid');

$t->comment('Use a bad href');

$form->bind(array('href' => 'bad href'), array());
$t->is($form->isValid(), false, 'The form is not valid');

$externalUrl = 'http://symfony-project.org';
$t->comment('Use a good href : '.$externalUrl);

$form->bind(array('href' => $externalUrl), array());
$t->is($form->isValid(), true, 'The form is valid');

$internalUrl = 'media:'.dmDb::table('DmMedia')->findOne()->id;
$t->comment('Use a good href : '.$internalUrl);

$form->bind(array('href' => $internalUrl), array());
$t->is($form->isValid(), true, 'The form is valid');

$internalUrl = 'page:'.dmDb::table('DmPage')->findOne()->id;
$t->comment('Use a good href : '.$internalUrl);

$form->bind(array('href' => $internalUrl), array());
$t->is($form->isValid(), true, 'The form is valid');

$t->comment('Save the widget');

$form->updateWidget()->save();

$t->ok($widget->exists(), 'Widget has been saved');
$expected = array(
  'href'        => $internalUrl,
  'text'        => '',
  'title'       => ''
);
ksort($expected);
$widgetValues = $widget->values;
ksort($widgetValues);
$t->is_deeply($widgetValues, $expected, 'Widget values are correct');

$t->comment('Recreate the form from the saved widget');

$form = new $formClass($widget);
$form->removeCsrfProtection();

$t->is($form->getDefault('href'), $internalUrl, 'The form default href is correct');

$t->comment('Submit form without additional data');
$form->bind($form->getDefaults(), array());
$t->is($form->isValid(), true, 'The form is valid');

$t->comment('Change widget options');
$form->bind(array_merge($form->getDefaults(), array(
  'text' => 'test text',
  'title' => 'test title',
  'cssClass' => 'test css_class',
)), array());
$t->is($form->isValid(), true, 'The form is valid');
if (!$form->isValid())
{
  $t->comment($form->getErrorSchema()->getMessage());
}

$t->comment('Save the widget');

$form->updateWidget()->save();

$t->ok($widget->exists(), 'Widget has been saved');
$t->is_deeply($widget->values, array(
  'href'        => $internalUrl,
  'text'        => 'test text',
  'title'       => 'test title'
), 'Widget values are correct');

$t->comment('Recreate the form from the saved widget');

$form = new $formClass($widget);
$form->removeCsrfProtection();

$t->is_deeply($widget->values, array(
  'href'        => $internalUrl,
  'text'        => 'test text',
  'title'       => 'test title'
), 'Widget values are correct');

$t->comment('Now display the widget');

$widgetArray = $widget->toArrayWithMappedValue();
ksort($widgetArray);
ksort($widgetArray);

$expected = array(
  'action' => 'link',
  'css_class' => 'test css_class',
  'dm_zone_id' => $widget->Zone->id,
  'id' => $widget->id,
  'module' => 'dmWidgetContent',
  'position' => $widget->position,
  'value' => json_encode(array(
    'href'        => $internalUrl,
    'text'        => 'test text',
    'title'       => 'test title'
  )),
  'updated_at' => $widget->updatedAt
);
ksort($expected);

$t->is_deeply($widgetArray, $expected, 'Widget array with mapped value is correct');

$helper->get('service_container')->setParameter('widget_renderer.widget', $widgetArray);

$widgetRenderer = $helper->get('service_container')->getService('widget_renderer');

// gather widget assets to load asynchronously
$stylesheets = array();
foreach($widgetRenderer->getStylesheets() as $stylesheet)
{
  $stylesheets[] = $helper->get('helper')->getStylesheetWebPath($stylesheet);
}
$js = '';
foreach($widgetRenderer->getJavascripts() as $javascript)
{
  $js .= file_get_contents($helper->get('helper')->getJavascriptFullPath($javascript)).';';
}

$t->is($stylesheets, array(), 'This widget view requires no additional stylesheet');
$t->is($js, '', 'This widget view requires no additional javascript');

$t->ok($widgetRenderer->getHtml(), 'The widget has been rendered');

$widgetView = $widgetRenderer->getWidgetView();

$t->isa_ok($widgetView, $widgetType->getOption('view_class'), 'The widget view is a '.$widgetType->getOption('view_class'));

$t->ok(!$widgetView->isRequiredVar('mediaId'), 'mediaId is not a view required var');
$t->ok($widgetView->isRequiredVar('href'), 'href is a view required var');

$expected = $helper->get('helper')->link($internalUrl)->addClass('test css_class')->text('test text')->title('test title')->render();
$t->is($widgetView->render(array('cssClass' => 'test css_class')), $expected, 'render : '.$expected);

$t->is($widgetView->renderForIndex(), $expected = 'test text test title', 'render for index is '.$expected);

/*
 * Clear the mess
 */
$testPage->PageView->delete();
$testPage->Node->delete();
$widget->delete();