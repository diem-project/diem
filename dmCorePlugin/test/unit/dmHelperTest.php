<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot('front');

$t = new lime_test(29);

dm::loadHelpers(array('Dm', 'I18N'));

$openDiv = '<div>';
$t->is(_open('div'), $openDiv, $openDiv);

$openDiv = '<div class="test_class">';
$t->is(_open('div.test_class'), $openDiv, $openDiv);

$openDiv = '<div id="test_id" class="test_class other_class">';
$t->is(_open('div#test_id.test_class.other_class'), $openDiv, $openDiv);

$openDiv = '<div id="test_id" class="test_class other_class">';
$t->is(_open('div', array('id' => 'test_id', 'class' => 'test_class other_class')), $openDiv, $openDiv);

$openDiv = '<div title="fancy title" id="test_id" class="first_class test_class other_class">';
$t->is(_open('div.first_class title="fancy title"', array('id' => 'test_id', 'class' => 'test_class other_class')), $openDiv, $openDiv);

$t->is(£('div'), $expected = '<div></div>', $expected);

$t->is(£('br'), $expected = '<br />', $div);

$t->is(£('hr'), $expected = '<hr />', $expected);

$t->is(£('img'), $expected = '<img />', $expected);

$t->is(£('input'), $expected = '<input />', $expected);

$div = '<div id="test_id" class="test_class other_class"></div>';
$t->is(£('div#test_id.test_class.other_class'), $div, $div);

$div = '<div id="test_id" class="test_class">div content</div>';
$t->is(£('div#test_id.test_class', 'div content'), $div, $div);

$div = '<div id="test_id" class="test_class">div content</div>';
$t->is(£('div#test_id.test_class', 'div content'), $div, $div);

$div = '<div class="'.htmlentities('{"attr":"value"}').'">div content</div>';
$t->is(£('div', array('json' => array('attr' => 'value')), 'div content'), $div, $div);

$div = '<div id="test_id" class="test_class '.htmlentities('{"attr":"value"}').'">div content</div>';
$t->is(£('div#test_id.test_class', array('json' => array('attr' => 'value')), 'div content'), $div, $div);

$div = '<div id="test_id" class="test_class '.htmlentities('{"attrs":["value1","value2"]}').'">div content</div>';
$t->is(£('div#test_id.test_class', array('json' => array('attrs' => array('value1', 'value2'))), 'div content'), $div, $div);

$a = '<a id="test_id" class="test_class" href="an_href#with_anchor">a content</a>';
$t->is(£('a#test_id.test_class href="an_href#with_anchor"', 'a content'), $a, $a);

$closeDiv = '</div>';
$t->is(£c('div'), $closeDiv, $closeDiv);

$dl = '<dl><dt>key</dt><dd>value</dd></dl>';
$t->is(definition_list(array('key' => 'value')), $dl, $dl);

$dl = '<dl class="test_class other_class"><dt>key</dt><dd>value</dd></dl>';
$t->is(definition_list(array('key' => 'value'), '.test_class.other_class'), $dl, $dl);

$div = '<div id="test_id" class="test_class other_class" title="title with a # inside"></div>';
$t->is(£('div#test_id.test_class.other_class title="title with a # inside"'), $div, $div);

$div = '<div id="test_id" class="test_class other_class" title="title with a #inside"></div>';
$t->is(£('div#test_id.test_class.other_class title="title with a #inside"'), $div, $div);

$div = '<div class="test_class other_class" title="title with a #inside"></div>';
$t->is(£('div.test_class.other_class title="title with a #inside"'), $div, $div);

$div = '<div class="test_class other_class" title="title with a .inside"></div>';
$t->is(£('div.test_class.other_class title="title with a .inside"'), $div, $div);

$div = '<div lang="c1"></div>';
$t->is(£('div lang=c1'), $div, $div);

$table = '<table><thead><tr><th>Header 1</th><th>Header 2</th></tr></thead></table>';
$t->is(_table()->head('Header 1', 'Header 2')->render(), $table, $table);

$table = '<table><thead><tr><th>Header 1</th><th>Header 2</th></tr></thead><tbody><tr><td>Value 1</td><td>Value 2</td></tr><tr><td>Value 3</td><td>Value 4</td></tr></tbody></table>';
$t->is(£table()->head('Header 1', 'Header 2')->body('Value 1', 'Value 2')->body('Value 3', 'Value 4')->render(), $table, $table);

$table = '<table><thead><tr><th>Header 1</th><th>Header 2</th></tr></thead><tbody><tr class="even"><td>Value 1</td><td>Value 2</td></tr><tr class="odd"><td>Value 3</td><td>Value 4</td></tr></tbody></table>';
$t->is(£table()->useStrip(true)->head('Header 1', 'Header 2')->body('Value 1', 'Value 2')->body('Value 3', 'Value 4')->render(), $table, $table);

$ctrlFullPath = dmOs::join(sfConfig::get('sf_web_dir'), 'dmCorePlugin/js/dmCoreCtrl.js');
$t->is($helper->get('helper')->getJavascriptFullPath('core.ctrl'), $ctrlFullPath, 'core ctrl is in '.$ctrlFullPath);