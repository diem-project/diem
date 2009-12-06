<?php

require_once(dirname(__FILE__).'/helper/dmTestHelper.php');
$helper = new dmTestHelper();
$helper->boot('front');

$t = new lime_test(10 + 3*count($helper->get('i18n')->getCultures()));

$user = $helper->get('user');

$index = $helper->get('search_index');
$index->setCulture($user->getCulture());

$t->pass('Index successfully created : '.$index->getName());

$stopWords = dmConfig::get('search_stop_words');

dmConfig::set('search_stop_words', 'un le  les de,du , da, di ,do   ou ');

$t->is(
  $index->getStopWords(),
  array('un', 'le', 'les', 'de', 'du', 'da', 'di', 'do', 'ou'),
  'stop words retrieved from site instance : '.implode(', ', $index->getStopWords())
);

dmConfig::set('search_stop_words', $stopWords);

$t->diag('Cleaning text');

$html = '<ol><li><a class="link dm_parent" href="symfony">Accueil</a></li><li>></li><li><a class="link dm_parent" href="symfony/domaines">Domaines</a></li><li>></li><li><a class="link dm_parent" href="symfony/domaines/77-cursus-proin1i471j0u">cursus. Proin1i471j0u</a></li><li>></li><li><a class="link dm_parent" href="symfony/domaines/77-cursus-proin1i471j0u/104-trices-interdum-risus-duisgpcinqn1">trices interdum risus. Duisgpcinqn1</a></li><li>></li><li><span class="link dm_current">Info : t, auctor ornare, risus. Donec lo</span></li></ol><span class="link dm_current">Info : t, auctor ornare, risus. Donec lo</span><div class="info_tag list_by_info"><ul class="elements"><li class="element clearfix"><a class="link" href="symfony/domaines/77-cursus-proin1i471j0u/104-trices-interdum-risus-duisgpcinqn1/t-auctor-ornare-risus-donec-lo-79/t-donec">t. Donec</a></li><li class="element clearfix"><a class="link" href="symfony/domaines/77-cursus-proin1i471j0u/104-trices-interdum-risus-duisgpcinqn1/t-auctor-ornare-risus-donec-lo-79/em-ipsu">em ipsu</a></li></ul></div>';
$expected = 'accueil domaines cursus proin1i471j0u trices interdum risus duisgpcinqn1 info t auctor ornare risus donec lo info t auctor ornare risus donec lo t donec em ipsu';
$t->is($index->cleanText($html), $expected, 'cleaned text : '.$expected);

$html = '<div>some content<a href="url">a link text  é àî</a>... end</div>';
$expected = 'some content a link text e ai end';
$t->is($index->cleanText($html), $expected, 'cleaned text : '.$expected);

$engine = $helper->get('search_engine');

$t->isa_ok($engine, 'dmSearchEngine', 'Got a dmSearchEngine instance');

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

$t->diag('Populate all indices');

$t->ok($engine->populate(), 'Indices populated');

$t->is($user->getCulture(), sfConfig::get('sf_default_culture'), 'User\'s culture has not been changed');

$t->diag('Optimize all indices');

$t->ok($engine->optimize(), 'Indices optimized');

$t->is($user->getCulture(), sfConfig::get('sf_default_culture'), 'User\'s culture has not been changed');

$indexDescription = $engine->getCurrentIndex()->describe();

$t->is($indexDescription['Documents'], $engine->getCurrentIndex()->getPagesQuery()->count(), 'All pages have been indexed : '.$indexDescription['Documents']);

$t->diag('Perform a search');

$engine->search('Diem');