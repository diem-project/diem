<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot();

$pageTable = dmDb::table('DmPage');
$root = $pageTable->getTree()->fetchRoot();

/*
 * get services
 */
$i18n = $helper->get('i18n');

/*
 * configure i18n and add 2 cultures
 */
$i18n->setCultures(array_merge(
  $i18n->getCultures(),
  array('c1', 'c2')
));
$helper->get('service_container')->mergeParameter('page_i18n_builder.options', array(
  'cultures' => $i18n->getCultures()
));

$i18nBuilder = $helper->get('page_i18n_builder');
$i18nBuilder->setOption('cultures', $i18n->getCultures());
/*
 * connect page_i18n_builder and set the new cultures
 */
if(!$i18nBuilder->isConnected())
{
  $i18nBuilder->connect();
}

$t = new lime_test(2 + 7*count($i18n->getCultures()));

$page = $randomCreatedPage = dmDb::create('DmPage', array(
  'module'      => 'test',
  'action'      => dmString::random(12),
  'slug'        => dmString::random(12),
  'name'        => dmString::random(12),
  'title'       => dmString::random(12),
  'h1'          => dmString::random(12),
  'description' => dmString::random(12),
  'keywords'    => dmString::random(12)
));

$page->getNode()->insertAsLastChildOf($root);

$t->ok($page->getCurrentTranslation()->exists(), 'The current translation exists');

$page->refreshRelated('Translation');

$pageTranslations = $page->get('Translation');

foreach($i18n->getCultures() as $culture)
{
  $pageTranslation = $pageTranslations->get($culture);
  
  $t->ok($pageTranslation->exists(), sprintf('The %s translation exists', $culture));
  
  foreach(array('slug', 'name', 'title', 'h1', 'description', 'keywords') as $field)
  {
    $t->is($pageTranslation->get($field), $page->get($field), sprintf('The %s translated page %s is %s', $culture, $field, $page->get($field)));
  }
}

$i18nBuilder->createAllPagesTranslations();

$pages = dmDb::table('DmPage')->createQuery('p')
->leftJoin('p.Translation')
->fetchRecords();

$nbMissingTranslations = 0;
foreach($pages as $page)
{
  $pageTranslations = $page->get('Translation');
  
  foreach($i18n->getCultures() as $culture)
  {
    if (!$pageTranslations->get($culture)->exists())
    {
      ++$nbMissingTranslations;
    }
  }
}
unset($pages);

$t->is($nbMissingTranslations, 0, 'All page translations have been created');