<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot('front');

clearstatcache(true);
$isSqlite = Doctrine_Manager::getInstance()->getConnection('doctrine') instanceof Doctrine_Connection_Sqlite;

$t = new lime_test(16 + ($isSqlite ? 0 : 1) + 3*count($helper->get('i18n')->getCultures()));

$user = $helper->get('user');

try
{
  $index = $helper->get('search_index');
  $t->fail('Can\'t create index without dir');
}
catch(dmSearchIndexException $e)
{
  $t->pass('Can\'t create index without dir');
}

$engine = $helper->get('search_engine');

$t->isa_ok($engine, 'dmSearchEngine', 'Got a dmSearchEngine instance');

$expected = dmProject::rootify(dmArray::get($helper->get('service_container')->getParameter('search_engine.options'), 'dir'));
$t->is($engine->getFullPath(), $expected, 'Current engine full path is '.$expected);

$t->ok(!file_exists(dmProject::rootify('segments.gen')), 'There is no segments.gen in project root dir');

$engine->setDir('cache/testIndex');

foreach($helper->get('i18n')->getCultures() as $culture)
{
  $user->setCulture($culture);
  $currentIndex = $engine->getCurrentIndex();
  $t->is($currentIndex->getName(), 'dm_page_'.$culture, 'Current index name is '.$currentIndex->getName());
  $t->is($currentIndex->getCulture(), $culture, 'Current index culture is '.$culture);
  $t->is($currentIndex->getFullPath(), dmProject::rootify('cache/testIndex/'.$currentIndex->getName()), 'Current index full path is '.$currentIndex->getFullPath());
}

$user->setCulture(sfConfig::get('sf_default_culture'));

$currentIndex = $engine->getCurrentIndex();
$t->isa_ok($currentIndex->getLuceneIndex(), 'Zend_Search_Lucene_Proxy', 'The current index is instanceof Zend_Search_Lucene_Proxy');

$t->ok(!file_exists(dmProject::rootify('segments.gen')), 'There is no segments.gen in project root dir');

dmConfig::set('search_stop_words', 'un le  les de,du , da, di ,do   ou ');

$t->is(
  $currentIndex->getStopWords(),
  array('un', 'le', 'les', 'de', 'du', 'da', 'di', 'do', 'ou'),
  'stop words retrieved from site instance : '.implode(', ', $currentIndex->getStopWords())
);

$t->diag('Cleaning text');

$html = '<ol><li><a class="link dm_parent" href="symfony">Accueil</a></li><li>></li><li><a class="link dm_parent" href="symfony/domaines">Domaines</a></li><li>></li><li><a class="link dm_parent" href="symfony/domaines/77-cursus-proin1i471j0u">cursus. Proin1i471j0u</a></li><li>></li><li><a class="link dm_parent" href="symfony/domaines/77-cursus-proin1i471j0u/104-trices-interdum-risus-duisgpcinqn1">trices interdum risus. Duisgpcinqn1</a></li><li>></li><li><span class="link dm_current">Info : t, auctor ornare, risus. Donec lo</span></li></ol><span class="link dm_current">Info : t, auctor ornare, risus. Donec lo</span><div class="info_tag list_by_info"><ul class="elements"><li class="element clearfix"><a class="link" href="symfony/domaines/77-cursus-proin1i471j0u/104-trices-interdum-risus-duisgpcinqn1/t-auctor-ornare-risus-donec-lo-79/t-donec">t. Donec</a></li><li class="element clearfix"><a class="link" href="symfony/domaines/77-cursus-proin1i471j0u/104-trices-interdum-risus-duisgpcinqn1/t-auctor-ornare-risus-donec-lo-79/em-ipsu">em ipsu</a></li></ul></div>';
$expected = 'accueil domaines cursus proin1i471j0u trices interdum risus duisgpcinqn1 info t auctor ornare risus donec lo info t auctor ornare risus donec lo t donec em ipsu';
$t->is($currentIndex->cleanText($html), $expected, 'cleaned text : '.$expected);

$html = '<div>some content<a href="url">a link text  é àî</a>... end</div>';
$expected = 'some content a link text e ai end';
$t->is($currentIndex->cleanText($html), $expected, 'cleaned text : '.$expected);

// for now this doesn't work on windows
if ('/' !== DIRECTORY_SEPARATOR) return;

$t->diag('Populate all indices');

$t->ok($engine->populate(), 'Indices populated');

$t->is($user->getCulture(), sfConfig::get('sf_default_culture'), 'User\'s culture has not been changed');

$t->ok(!file_exists(dmProject::rootify('segments.gen')), 'There is no segments.gen in project root dir');

$t->diag('Optimize all indices');

$t->ok($engine->optimize(), 'Indices optimized');

$t->is($user->getCulture(), sfConfig::get('sf_default_culture'), 'User\'s culture has not been changed');

$t->ok(!file_exists(dmProject::rootify('segments.gen')), 'There is no segments.gen in project root dir');

$t->ok(file_exists(dmProject::rootify('cache/testIndex/'.$currentIndex->getName(), 'segments.gen')), 'There is segments.gen in cache index dir ' . dmProject::rootify('cache/testIndex/'.$currentIndex->getName(), 'segments.gen'));

$indexDescription = $engine->getCurrentIndex()->describe();

// not pages indexed with sqlite (?)
if(!$isSqlite)
{
	$t->is($indexDescription['Documents'], $engine->getCurrentIndex()->getPagesQuery()->count(), 'All pages have been indexed : '.$indexDescription['Documents']);
	$t->diag('Perform a search');
	
	$engine->search('Diem');
}

