<?php

require_once(realpath(dirname(__FILE__).'/../../..').'/unit/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot();

$t = new lime_test(32);

$t->comment('Testing DmTestPost');

$model = 'DmTestPost';
$post = new $model;
$post->Categ = dmDb::create('DmTestCateg', array('name' => dmString::random()))->saveGet();
$post->userId = dmDb::table('DmUser')->findOne()->id;
$post->setDateTimeObject('date', new DateTime());
$table = dmDb::table($model);

$t->ok($table->isVersionable(), $model.' is versionable');

$t->is($post->version, 0, 'new record, version is 0');

$post->title = dmString::random();
$post->title = 'jefferson';
$post->save();

$t->is($post->version, 1, 'saved record, version is 1');
$t->is($post->title, 'jefferson', 'saved record, title is jefferson');

$post->title = 'airplane';
$post->save();

$t->is($post->version, 2, 'saved record, version is 2');
$t->is($post->title, 'airplane', 'saved record, title is airplane');

$post->revert(1);

$t->is($post->version, 1, 'reverted but not saved, record version is 1');
$t->is($post->title, 'jefferson', 'reverted but not saved, record title is jefferson');

$post->save();

$t->is($post->version, 3, 'reverted and saved, record version is 3');
$t->is($post->title, 'jefferson', 'reverted and saved, record title is jefferson');

$t->is(count($post->getCurrentTranslation()->Version), 3, 'record has 3 versions');

$post->title = 'jethro';
$post->save();

$t->is($post->version, 4, 'saved record, version is 4');
$t->is($post->title, 'jethro', 'saved record, title is jethro');

// now with another culture
$id = $post->id;
$post->free(true);
unset($post);
$post = $table->findOne($id);

$helper->get('user')->setCulture('c1');

$post->title = 'c1 title';

$post->save();

$t->is($post->version, 1, 'saved record, version is 1');
$t->is($post->title, 'c1 title', 'saved record, title is c1 title');

$t->comment('Testing DmTestComment');

$model = 'DmTestComment';
$table = dmDb::table($model);
$comment = $table->create(array(
  'post_id' => $post->id
));

$t->ok($table->isVersionable(), $model.' is versionable');

$t->is($comment->version, 0, 'new record, version is 0');

$comment->author = dmString::random();
$comment->author = 'jefferson';
$comment->save();

$t->is($comment->version, 1, 'saved record, version is 1');
$t->is($comment->author, 'jefferson', 'saved record, author is jefferson');

$comment->author = 'airplane';
$comment->save();

$t->is($comment->version, 2, 'saved record, version is 2');
$t->is($comment->author, 'airplane', 'saved record, author is airplane');

$comment->revert(1);

$t->is($comment->version, 1, 'reverted but not saved, record version is 1');
$t->is($comment->author, 'jefferson', 'reverted but not saved, record author is jefferson');

$comment->save();

$t->is($comment->version, 3, 'reverted and saved, record version is 3');
$t->is($comment->author, 'jefferson', 'reverted and saved, record author is jefferson');

$t->is(count($comment->Version), 3, 'record has 3 versions');

$comment->author = 'jethro';
$comment->save();

$t->is($comment->version, 4, 'saved record, version is 4');
$t->is($comment->author, 'jethro', 'saved record, author is jethro');

$t->comment('Disable versioning');

$comment->mapValue('disable_versioning', true);
$comment->author = 'jimmy';
$comment->save();

$t->is($comment->version, 4, 'saved record, version is 4');
$t->is($comment->author, 'jimmy', 'saved record, author is jimmy');

$t->comment('Enable versioning');

$comment->mapValue('disable_versioning', false);
$comment->author = 'crimson';
$comment->save();

$t->is($comment->version, 5, 'saved record, version is 4');
$t->is($comment->author, 'crimson', 'saved record, author is crimson');

$comment->delete();

$post->delete();