<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot('front');

$t = new lime_test(21);

$forms = $helper->get('form_manager');

$t->isa_ok($forms, 'dmFormManager', '$forms is a form manager');

try
{
  $form = $forms['non_existing_form'];
  $t->fail('Create non existing form');
}
catch(Exception $e)
{
  $t->pass('Create non existing form');
}
//DmUserForm without widgets option will instanciate all widgets
//here CreatedByDmTestFruits is causing a problem.
//the actual test does not care about it, so only take care of username
$forms['dmUser'] = new DmUserForm(null, array('widgets' => array('username', 'password', 'email')));

$form = $forms['dmUser'];
$widgetSchema = $form->getWidgetSchema();

$t->isa_ok($form, 'DmUserForm', 'Created a DmUserForm');

$form->setName('first_dmUser_form');

$widgetSchema->setHelp('username', 'username help');

$t->is($form->getName(), 'first_dmUser_form', 'Changed form name to first_dmUser_form');

unset($form);

$form = $forms['dmUser'];

$t->isa_ok($form, 'DmUserForm', 'Got a DmUserForm');

$t->is($form->getName(), 'first_dmUser_form', 'It is the same one');

$t->isa_ok($form['username'], 'dmFormField', 'got a dmFormField');

$got = (string)$form['username']->label('label_test');
$expected = '<label class="label" for="first_dmUser_form_username">label_test</label>';
$t->is($got, $expected, $got);

$got = (string)$form['username']->help();
$expected = '<div class="dm_help_wrap">username help</div>';
$t->is($got, $expected, $got);

$got = (string)$form['username']->help('another help');
$expected = '<div class="dm_help_wrap">another help</div>';
$t->is($got, $expected, $got);

$got = (string)$form['username']->error();
$expected = '';
$t->is($got, $expected, $got);

$got = (string)$form['username']->field();
$expected = '<input type="text" name="first_dmUser_form[username]" class="required" id="first_dmUser_form_username" />';
$t->is($got, $expected, $got);

$got = (string)$form['username']->label('label_test')->field()->help()->error();
$expected = '<label class="label" for="first_dmUser_form_username">label_test</label><input type="text" name="first_dmUser_form[username]" class="required" id="first_dmUser_form_username" /><div class="dm_help_wrap">username help</div>';
$t->is($got, $expected, $got);

$t->is(substr_count((string)$form, 'first_dmUser_form[_csrf_token]'), 1, 'CRSF protection outputed once');

$t->is(substr_count($form->open().$form->render().$form->close(), 'first_dmUser_form[_csrf_token]'), 1, 'CRSF protection outputed once');

$t->is(substr_count($form->open().$form['username'].$form->renderHiddenFields().$form->close(), 'first_dmUser_form[_csrf_token]'), 1, 'CRSF protection outputed once');

$t->is(substr_count($form->open().$form['username'].$form['password'].$form->renderHiddenFields().$form->close(), 'first_dmUser_form[_csrf_token]'), 1, 'CRSF protection outputed once');

$t->is(substr_count($form->open().$form['username']->label()->error()->field().$form['password']->label()->error()->field().$form->renderHiddenFields().$form->close(), 'first_dmUser_form[_csrf_token]'), 1, 'CRSF protection outputed once');

$widgetSchema['username']->setAttribute('class', 'test_class_1');
$got = (string)$form['username']->render();
$expected = '<input class="test_class_1" type="text" name="first_dmUser_form[username]" id="first_dmUser_form_username" />';
$t->is($got, $expected, $got);

$got = (string)$form['username']->field();
$expected = '<input class="test_class_1 required" type="text" name="first_dmUser_form[username]" id="first_dmUser_form_username" />';
$t->is($got, $expected, $got);

$got = (string)$form['username']->field('.other_class');
$expected = '<input class="test_class_1 other_class required" type="text" name="first_dmUser_form[username]" id="first_dmUser_form_username" />';
$t->is($got, $expected, $got);