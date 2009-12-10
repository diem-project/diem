<?php

require_once dmOs::join(sfConfig::get('dm_core_dir'), 'test/unit/helper/dmUnitTestHelper.php');

$helper = new dmUnitTestHelper();

$helper->boot('front');

// load both standard and alternative helpers
dm::loadHelpers(array('Dm', 'DmAlternative', 'I18N'));

$t = new lime_test(30);

/*

* $helper is the test helper. It acts as a service container
* and can provide you all services with its get() method.
* so here we get the 'helper' service which is the template helper instance
*/
$templateHelper = $helper->get('helper');

// £ function
$t->is(£('div', 'content'), _tag('div', 'content'), '£ == _tag');

// £ method
$t->is($templateHelper->£('div', 'content'), $templateHelper->_tag('div', 'content'), '£ == _tag');
$t->is(£('div', 'content'), _tag('div', 'content'), '£ == _tag');

// £link method
$t->is((string)$templateHelper->£link()->text('home'), (string)$templateHelper->_link()->text('home'), '£link == _link'); 
$t->is((string)£link()->text('home'), (string)_linkTag()->text('home'), '£link == _link');


$openDiv = '<div>';
$t->is(£o('div'), _tagO('div'), $openDiv);

$openDiv = '<div class="test_class">';
$t->is(£o('div.test_class'), _tagO('div.test_class'), $openDiv);

$openDiv = '<div id="test_id" class="test_class other_class">';
$t->is(£o('div#test_id.test_class.other_class'), _tagO('div#test_id.test_class.other_class'), $openDiv);

$openDiv = '<div class="test_class other_class" id="test_id">';
$t->is(
  £o('div', array('id' => 'test_id', 'class' => 'test_class other_class')), 
  _tagO('div', array('id' => 'test_id', 'class' => 'test_class other_class')), 
  $openDiv
);

$div = '<div></div>';
$t->is(£('div'), _tag('div'), $div);

$div = '<div class="'.htmlentities('{"attr":"value"}').'">div content</div>';
$t->is(
  $templateHelper->£('div', array('json' => array('attr' => 'value')), 'div content'), 
  _tag('div', array('json' => array('attr' => 'value')), 'div content'), 
  $div
);
$t->is(
  £('div', array('json' => array('attr' => 'value')), 'div content'), 
  _tag('div', array('json' => array('attr' => 'value')), 'div content'), 
  $div
);

$a = '<a href="an_href#with_anchor" id="test_id" class="test_class">a content</a>';
$t->is(
  $templateHelper->£('a#test_id.test_class href="an_href#with_anchor"', 'a content'), 
  _tag('a#test_id.test_class href="an_href#with_anchor"', 'a content'), 
  $a
);
$t->is(
  £('a#test_id.test_class href="an_href#with_anchor"', 'a content'), 
  _tag('a#test_id.test_class href="an_href#with_anchor"', 'a content'), 
  $a
);

$closeDiv = '</div>';
$t->is(£c('div'), _tagC('div'), $closeDiv);

$div = '<div title="title with a # inside" id="test_id" class="test_class other_class"></div>';
$t->is(
  $templateHelper->£('div#test_id.test_class.other_class title="title with a # inside"'), 
  _tag('div#test_id.test_class.other_class title="title with a # inside"'), 
  $div
);
$t->is(
  £('div#test_id.test_class.other_class title="title with a # inside"'), 
  _tag('div#test_id.test_class.other_class title="title with a # inside"'), 
  $div
);

$div = '<div title="title with a #inside" id="test_id" class="test_class other_class"></div>';
$t->is(
  $templateHelper->£('div#test_id.test_class.other_class title="title with a #inside"'), 
  _tag('div#test_id.test_class.other_class title="title with a #inside"'), 
  $div
);
$t->is(
  £('div#test_id.test_class.other_class title="title with a #inside"'), 
  _tag('div#test_id.test_class.other_class title="title with a #inside"'), 
  $div
);

$div = '<div title="title with a #inside" class="test_class other_class"></div>';
$t->is(
  $templateHelper->£('div.test_class.other_class title="title with a #inside"'), 
  _tag('div.test_class.other_class title="title with a #inside"'), 
  $div
);
$t->is(
  £('div.test_class.other_class title="title with a #inside"'), 
  _tag('div.test_class.other_class title="title with a #inside"'), 
  $div
);

$div = '<div title="title with a .inside" class="test_class other_class"></div>';
$t->is(
  $templateHelper->£('div.test_class.other_class title="title with a .inside"'), 
  _tag('div.test_class.other_class title="title with a .inside"'), 
  $div
);
$t->is(
  £('div.test_class.other_class title="title with a .inside"'), 
  _tag('div.test_class.other_class title="title with a .inside"'), 
  $div
);

$div = '<div lang="c1"></div>';
$t->is($templateHelper->£('div lang=c1'), _tag('div lang=c1'), $div);
$t->is(£('div lang=c1'), _tag('div lang=c1'), $div);

$div = '<div></div>';
$t->is(£('div lang='.$helper->get('user')->getCulture()), $div, $div);

$table = '<table><thead><tr><th>Header 1</th><th>Header 2</th></tr></thead></table>';
$t->is(
  £table()->head('Header 1', 'Header 2')->render(), 
  _table()->head('Header 1', 'Header 2')->render(), 
  $table
);
$t->is(
  $templateHelper->£table()->head('Header 1', 'Header 2')->render(), 
  _table()->head('Header 1', 'Header 2')->render(), 
  $table
);

$table = '<table><thead><tr><th>Header 1</th><th>Header 2</th></tr></thead><tbody><tr class="even"><td>Value 1</td><td>Value 2</td></tr><tr class="odd"><td>Value 3</td><td>Value 4</td></tr></tbody></table>';
$t->is(
  $templateHelper->£table()->head('Header 1', 'Header 2')->body('Value 1', 'Value 2')->body('Value 3', 'Value 4')->render(), 
  _table()->head('Header 1', 'Header 2')->body('Value 1', 'Value 2')->body('Value 3', 'Value 4')->render(), 
  $table
);
$t->is(
  £table()->head('Header 1', 'Header 2')->body('Value 1', 'Value 2')->body('Value 3', 'Value 4')->render(), 
  _table()->head('Header 1', 'Header 2')->body('Value 1', 'Value 2')->body('Value 3', 'Value 4')->render(), 
  $table
);