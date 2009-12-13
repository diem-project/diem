<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot();

$t = new lime_test(5);

$t->comment('Veriy if web/cache and cache/web are synchronized, even on non Unix system when no symlinks are available');

$cm = $helper->get('cache_manager');

$fs = $helper->get('filesystem');

$finder = sfFinder::type('all');

$webCacheDir = dmOs::join(sfConfig::get('sf_web_dir'), 'cache');
$cacheWebDir = dmOs::join(sfConfig::get('sf_cache_dir'), 'web');

$cm->clearAll();

$t->is(array(), $finder->in($webCacheDir), '/web/cache is empty');

$t->is(array(), $finder->in($cacheWebDir), '/cache/web is empty');

$fs->mkdir($cacheWebDir);

touch($webCacheDir.'/test');

$t->is(1, count($finder->in($webCacheDir)), '/web/cache contains a file');

$t->comment('Run cache:clear task');
$task = new sfCacheClearTask($helper->get('dispatcher'), new sfFormatter);
$task->execute();

$t->is(1, count($finder->in($webCacheDir)), '/web/cache still contains a file');

$t->comment('execute dmFileCache::clearAll()');
dmFileCache::clearAll();

$t->is(array(), $finder->in($webCacheDir), '/web/cache is empty');