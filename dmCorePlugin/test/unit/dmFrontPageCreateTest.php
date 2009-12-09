<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot('front');

$t = new lime_test();

// create form
require_once(dmOs::join(sfConfig::get('dm_front_dir'), 'modules/dmPage/lib/form/DmPageFrontNewForm.php'));
$form = new DmPageFrontNewForm;
$form->removeCsrfProtection();

// create layout
$layout = dmDb::create('DmLayout', array(
  'name' => dmString::random()
))->saveGet();

// create parent page
$parentPage = dmDb::table('DmPage')->find(array_rand($form->getWidgetSchema()->offsetGet('parent_id')->getChoices()));

// create name and slug
$pageName = $pageSlug = dmString::random();

$form->bind(array(
  'name' => $pageName,
  'slug' => $pageSlug,
  'dm_layout_id' => $layout->id,
  'parent_id' => $parentPage->id
));

try
{
  $page = $form->save();
  
  $t->pass('The page has been saved');
}
catch(Exception $e)
{
  $t->fail('Page creation failed with exception '.$e->getMessage());
}

// get data from db
$page->refresh(true);

$t->isa_ok($page, 'DmPage', 'The page is an instance of DmPage');

$t->ok($page->hasCurrentTranslation(), 'The page has a current translation');

$t->is($page->name, $pageName, 'The page name is '.$pageName);

$t->is($page->title, $pageName, 'The page title is '.$pageName);

$t->is($page->slug, $pageSlug, 'The page slug is '.$pageSlug);

$t->is($page->module, $parentPage->module, 'The page module is the parent page module : '.$parentPage->module);

$t->is($page->PageView->Layout, $layout, 'The layout has been applied');

$t->is($page->Node->getParent(), $parentPage, 'The page has been inserted in its parent');

$page->delete();
$layout->delete();