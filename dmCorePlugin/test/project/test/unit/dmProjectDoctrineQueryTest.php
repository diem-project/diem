<?php

require_once(realpath(dirname(__FILE__).'/../../..').'/unit/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot();

$t = new lime_test();

$t->comment('Test getJoinAliasForRelationAlias');

$query = dmDb::query('DmTestCateg c');

$t->is($query->getJoinAliasForRelationAlias('DmTestCateg', 'Translation'), null);

$query = dmDb::query('DmTestCateg c')->withI18n();

$t->is($query->getJoinAliasForRelationAlias('DmTestCateg', 'Translation'), 'cTranslation');

$query = dmDb::query('DmTestCateg c')
->withI18n()
->leftJoin('c.Posts p')
->leftJoin('p.Translation pTranslation WITH pTranslation.lang = ?', 'en');

$t->is($query->getJoinAliasForRelationAlias('DmTestCateg', 'Translation'), 'cTranslation');
$t->is($query->getJoinAliasForRelationAlias('DmTestPost', 'Translation'), 'pTranslation');