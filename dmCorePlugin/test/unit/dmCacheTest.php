<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot();

dmAPCCache::enable(false);

$t = new lime_test(7);

$cache = dmContext::getInstance()->getCacheManager()->getCache("dm/test");

$t->ok(
  $cache instanceof dmMetaCache,
  "obtention d'une instance de dmMetaCache"
);

$t->ok(
  $cache->getCache() instanceof dmFileCache,
  "dmMetaCache utilise dmFileCache"
);

$t->ok(
  $cache->get("key") === null,
  "le cache[key] est vide"
);

$cache->set("key", "value");

$t->ok(
  $cache->get("key") === "value",
  "cache[key] contient value"
);

$cache->remove("key");

$t->ok(
  $cache->get("key") === null,
  "le cache[key] a été supprimé"
);

$cache->set("key", "value");

$t->ok(
  $cache->get("key") === "value",
  "cache[key] contient value"
);

$cache->clean();

$t->ok(
  $cache->get("key") === null,
  "le cache[key] a été supprimé"
);