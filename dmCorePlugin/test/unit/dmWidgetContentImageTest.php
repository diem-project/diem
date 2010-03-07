<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot('front');

$t = new lime_test(34);

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

$wtm = $helper->get('widget_type_manager');

$widgetType = $wtm->getWidgetType('dmWidgetContent', 'image');

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

$form = new $formClass($widget);
$form->removeCsrfProtection();

$html = $form->render();
$t->like($html, '_^<form\s(.|\n)*</form>$_', 'Successfully obtained and rendered a '.$formClass.' instance');

$t->is($form->getStylesheets(), array(), 'This widget form requires no additional stylesheet');
$t->is($form->getJavascripts(), array(), 'This widget form requires no additional javascript');

$t->comment('Submit an empty form');

$form->bind(array(), array());
$t->is($form->isValid(), false, 'The form is not valid');

$t->comment('Use a bad media id');

$form->bind(array('mediaId' => 9999999999999), array());
$t->is($form->isValid(), false, 'The form is not valid');

$t->comment('Use a good media id : '.$media->id);

$form->bind(array('mediaId' => $media->id), array());
$t->is($form->isValid(), true, 'The form is valid');

$t->comment('Save the widget');

$form->updateWidget()->save();

$t->ok($widget->exists(), 'Widget has been saved');
$expected = array(
  'mediaId'     => $media->id,
  'legend'      => '',
  'width'       => '1000',
  'height'      => '',
  'method'      => dmConfig::get('image_resize_method'),
  'background'  => 'FFFFFF',
  'quality'     => NULL,
  'link'        => ''
);
ksort($expected);
$widgetValues = $widget->values;
ksort($widgetValues);
$t->is_deeply($widgetValues, $expected, 'Widget values are correct');

$t->comment('Recreate the form from the saved widget');

$form = new $formClass($widget);
$form->removeCsrfProtection();

$t->is($form->getDefault('mediaId'), $media->id, 'The form default mediaId is correct');

$t->comment('Submit form without additional data');
$form->bind($form->getDefaults(), array());
$t->is($form->isValid(), true, 'The form is valid');

$t->comment('Change widget options');
$form->bind(array_merge($form->getDefaults(), array(
  'legend' => 'test legend',
  'width'  => 300,
  'height' => 200,
  'cssClass' => 'test css_class',
  'method' => 'fit',
  'quality' => 50,
  'link' => 'http://diem-project.org'
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
  'mediaId'     => $media->id,
  'legend'      => 'test legend',
  'width'       => '300',
  'height'      => '200',
  'method'      => 'fit',
  'background'  => 'FFFFFF',
  'quality'     => 50,
  'link' => 'http://diem-project.org'
), 'Widget values are correct');

$t->comment('Recreate the form from the saved widget');

$form = new $formClass($widget);
$form->removeCsrfProtection();

$t->comment('Bind with an uploaded file');

$media2FileName = 'test_'.dmString::random().'.jpg';
$media2FullPath = sys_get_temp_dir().'/'.$media2FileName;
copy($media->fullPath, $media2FullPath);

$form->bind($form->getDefaults(), array(
  'file' => array(
    'name' => $media2FileName,
    'type' => $helper->get('mime_type_resolver')->getByFilename($media2FullPath),
    'tmp_name' => $media2FullPath,
    'error' => 0,
    'size' => filesize($media2FullPath)
  )
));
$t->is($form->isValid(), true, 'The form is valid');

$t->comment('Save the widget');

$form->updateWidget()->save();

$t->ok($widget->exists(), 'Widget has been saved');

$t->isnt($widget->values['mediaId'], $media->id, 'The widget mediaId value has changed');

$media2 = dmDb::table('DmMedia')->find($widget->values['mediaId']);

$t->ok($media2->exists(), 'A new DmMedia record has been created');

$t->is_deeply($widget->values, array(
  'mediaId'     => $media2->id,
  'legend'      => 'test legend',
  'width'       => '300',
  'height'      => '200',
  'method'      => 'fit',
  'background'  => 'FFFFFF',
  'quality'     => 50,
  'link'        => 'http://diem-project.org'
), 'Widget values are correct');

$t->comment('Now display the widget');

$widgetArray = $widget->toArrayWithMappedValue();
ksort($widgetArray);
ksort($widgetArray);

$expected = array(
  'action' => 'image',
  'css_class' => 'test css_class',
  'dm_zone_id' => $widget->Zone->id,
  'id' => $widget->id,
  'module' => 'dmWidgetContent',
  'position' => $widget->position,
  'value' => json_encode(array(
    'mediaId'     => $media2->id,
    'width'       => '300',
    'height'      => '200',
    'legend'      => 'test legend',
    'method'      => 'fit',
    'background'  => 'FFFFFF',
    'quality'     => 50,
    'link'        => 'http://diem-project.org'
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

$t->ok($widgetView->isRequiredVar('mediaId'), 'mediaId is a view required var');
$t->ok($widgetView->isRequiredVar('method'), 'method is a view required var');

$viewVars = $widgetView->getViewVars();
$mediaTag = $viewVars['mediaTag'];

$t->isa_ok($mediaTag, 'dmMediaTagImage', 'The media tag is a dmMediaTagImage');

$t->is($mediaTag->get('resize_method'), 'fit', 'media tag resize_method is fit');

$t->is($mediaTag->get('resize_quality'), 50, 'media tag resize_quality is 50');

$t->is($mediaTag->get('background'), 'FFFFFF', 'media tag background is 50');

$t->is($mediaTag->get('alt'), 'test legend', 'media tag alt is test legend');

$t->is($mediaTag->get('width'), 300, 'media tag width is 300');

$t->is($mediaTag->get('height'), 200, 'media tag height is 200');

$t->is_deeply($mediaTag->get('class'), array(), 'media tag css_class is empty');

/*
 * Clear the mess
 */
$testPage->PageView->delete();
$testPage->Node->delete();
$widget->delete();
$media->delete();
$media2->delete();