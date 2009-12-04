<?php

require_once(dirname(__FILE__).'/helper/dmTestHelper.php');
$helper = new dmTestHelper();
$helper->boot('front');

$t = new lime_test(13);

dm::loadHelpers(array('Dm', 'I18N'));

$openDiv = '<div>';
$t->is(£o('div'), $openDiv, $openDiv);

$openDiv = '<div class="test_class">';
$t->is(£o('div.test_class'), $openDiv, $openDiv);

$openDiv = '<div id="test_id" class="test_class other_class">';
$t->is(£o('div#test_id.test_class.other_class'), $openDiv, $openDiv);

$openDiv = '<div class="test_class other_class" id="test_id">';
$t->is(£o('div', array('id' => 'test_id', 'class' => 'test_class other_class')), $openDiv, $openDiv);

$div = '<div></div>';
$t->is(£('div'), $div, $div);

$div = '<div id="test_id" class="test_class other_class"></div>';
$t->is(£('div#test_id.test_class.other_class'), $div, $div);

$div = '<div id="test_id" class="test_class">div content</div>';
$t->is(£('div#test_id.test_class', 'div content'), $div, $div);

$dl = '<dl><dt>key</dt><dd>value</dd></dl>';
$t->is(definition_list(array('key' => 'value')), $dl, $dl);

$dl = '<dl class="test_class other_class"><dt>key</dt><dd>value</dd></dl>';
$t->is(definition_list(array('key' => 'value'), '.test_class.other_class'), $dl, $dl);

$div = '<div title="title with a # inside" id="test_id" class="test_class other_class"></div>';
$t->is(£('div#test_id.test_class.other_class title="title with a # inside"'), $div, $div);

$div = '<div title="title with a #inside" id="test_id" class="test_class other_class"></div>';
$t->is(£('div#test_id.test_class.other_class title="title with a #inside"'), $div, $div);

$div = '<div title="title with a #inside" class="test_class other_class"></div>';
$t->is(£('div.test_class.other_class title="title with a #inside"'), $div, $div);

$div = '<div title="title with a .inside" class="test_class other_class"></div>';
$t->is(£('div.test_class.other_class title="title with a .inside"'), $div, $div);