<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot('front');

$t = new lime_test(24);

dmDb::table('DmPage')->checkBasicPages();

// create form
require_once(dmOs::join(sfConfig::get('dm_front_dir'), 'modules/dmPage/lib/DmPageFrontNewForm.php'));
$form = new DmPageFrontNewForm;
$form->removeCsrfProtection();

// create layout
$layout = dmDb::create('DmLayout', array(
  'name' => dmString::random()
))->saveGet();

// create parent page
$parentPage = dmDb::table('DmPage')->find(array_rand($form->getWidgetSchema()->offsetGet('parent_id')->getChoices()));

$t->comment('Choosed parent : '.$parentPage);

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

$t->is($page->Node->getParent()->__toString(), $parentPage->__toString(), 'The page has been inserted in its parent');

$t->is($page->getNodeParentId(), $parentPage->id, 'The page node parent id is '.$parentPage->id);

$t->is($page->recordId, 0, 'The page has no record id');

$t->is($page->isSecure, false, 'The page is not secured');

$t->is($page->isActive, true, 'The page is active');

$t->is($page->isIndexable, true, 'The page is indexable');

$t->is($page->lang, $lang = sfConfig::get('sf_default_culture'), 'The page lang is '.$lang);

$t->is($page->autoMod, 'snthdk', 'The page automod is snthdk');

$t->is($page->getIsAutomatic(), false, 'The page is not automatic');

$pageView = $page->PageView;
$layout = $pageView->Layout;

$t->comment('Delete page');
$page->Node->delete();

$t->is($page->getNameBackup(), $pageName, 'The deleted page name backup is '.$pageName);

$t->ok(!$page->exists(), 'The page no more exists');
$t->ok($pageView->exists(), 'The page view still exists');
$t->ok($layout->exists(), 'The layout still exists');

$t->comment('Delete page view');
$pageView->delete();

$t->ok(!$pageView->exists(), 'The page view no more exists');
$t->ok($layout->exists(), 'The layout still exists');

$t->comment('Delete layout');
$layout->delete();
$t->ok(!$layout->exists(), 'The layout no more exists');