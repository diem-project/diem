<?php

require_once(realpath(dirname(__FILE__).'/../../..').'/unit/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot();

$t = new lime_test();

$t->comment('Testing DmTestPost');

$helper->loremizeModule('dmTestCateg', 3, $t);
$helper->loremizeModule('dmTestPost', 10, $t);

$post = dmDb::query('DmTestPost p')->orderBy('p.position ASC')->whereIsActive(true, 'DmTestPost')->fetchOne();

$t->ok(!$post->getPrevious(), 'first post has no previous post');

while($next = $post->getNext())
{
  $t->ok($next->position > $post->position, 'next position is greater');
  $t->ok($next->isActive, 'next post is active');
  
  $t->is($next->getPrevious(), $post, 'next previous is the current post');
  
  $post = $next;
}