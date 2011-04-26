<?php

require_once(realpath(dirname(__FILE__).'/../../..').'/unit/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot();

$t = new lime_test(20);

$t->comment('Testing DmTestPost');

dmDb::create('DmTestCateg', array('name' => dmString::random()))->save();
$helper->loremizeModule('dmTestPost', 10, $t);

foreach(dmDb::query('DmTestPost p')->whereIsActive(true, 'DmTestPost')->fetchRecords() as $post)
{
  $t->ok($post->isActive, 'post '.$post.' is active');
}

foreach(dmDb::query('DmTestPost p')->whereIsActive(false, 'DmTestPost')->fetchRecords() as $post)
{
  $t->ok(!$post->isActive, 'post '.$post.' is NOT active');
}