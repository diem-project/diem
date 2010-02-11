<?php

require_once(realpath(dirname(__FILE__).'/../../..').'/unit/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot();

$categ = dmDb::create('DmTestCateg', array('name' => dmString::random()))->saveGet();
$user = dmDb::table('DmUser')->findOne();

$t = new lime_test(44);

$t->comment('Create a post without media');

$form = new DmTestPostForm();

$form->bind(array(), array());

$t->is($form->isValid(), false, 'Empty form is not valid');

$form->bind(array(
  'title' => dmString::random(),
  'categ_id' => $categ->id,
  'user_id' => $user->id,
  'date' => time()
), array());

$t->is($form->isValid(), true, 'Form with binded title is valid');

$post = $form->save();

$t->is($post->exists(), true, 'The post has been created');

$t->is($post->Image->exists(), false, 'The post has an empty new media');

$postId = $post->id;

$t->comment('Add an uploaded media to the post');

$form = new DmTestPostForm($post);

$media1Source   = dmOs::join(sfConfig::get('sf_upload_dir'), 'images/default.jpg');
$media1FileName = 'test_'.dmString::random().'.jpg';
$media1FullPath = sys_get_temp_dir().'/'.$media1FileName;
copy($media1Source, $media1FullPath);
$form->bind(array(
  'id'    => $post->id,
  'title' => $post->title,
  'categ_id' => $categ->id,
  'user_id' => $user->id,
  'date' => time(),
  'image_id_form' => array(
    'dm_media_folder_id' => $post->getDmMediaFolder()->get('id')
  )
), array(
  'image_id_form' => array('file' => array(
    'name' => $media1FileName,
    'type' => $helper->get('mime_type_resolver')->getByFilename($media1FullPath),
    'tmp_name' => $media1FullPath,
    'error' => 0,
    'size' => filesize($media1FullPath)
  )))
);

$t->is($form->isValid(), true, 'Form with binded title and image is valid');

$post = $form->save();

$t->is($postId, $post->id, 'The same post has been updated');

$t->is($post->Image->exists(), true, 'The post has an existing media');

$media1 = $post->Image;
$t->is($media1->file, $media1FileName, 'Post media filename is '.$media1FileName);
$t->is($media1->size, filesize($media1FullPath), 'Post media size is '.filesize($media1FullPath));

$t->comment('Resave the post without uploading media');

$form = new DmTestPostForm($post);

$form->bind(array(
  'id'    => $post->id,
  'title' => $post->title,
  'categ_id' => $categ->id,
  'user_id' => $user->id,
  'date' => time(),
  'image_id_form' => array(
    'id' => $post->Image->id,
    'dm_media_folder_id' => $post->Image->Folder->id
  )
), array()
);

$t->is($form->isValid(), true, 'Form with binded title is valid');

$post = $form->save();

$t->is($postId, $post->id, 'The same post has been updated');

$t->is($post->Image->exists(), true, 'The post has an existing media');

$t->is($post->Image->file, $media1FileName, 'Post media filename is '.$media1FileName);
$t->is($post->Image->size, filesize($media1FullPath), 'Post media size is '.filesize($media1FullPath));

$t->comment('Remove the media from the post');

$form = new DmTestPostForm($post);

$form->bind(array(
  'id'    => $post->id,
  'title' => $post->title,
  'categ_id' => $categ->id,
  'user_id' => $user->id,
  'date' => time(),
  'image_id_form' => array(
    'id' => $post->Image->id,
    'dm_media_folder_id' => $post->Image->Folder->id,
    'remove' => true
  )
), array()
);

$t->is($form->isValid(), true, 'Form with binded title and remove is valid');

$post = $form->save();

$t->is($postId, $post->id, 'The same post has been updated');

$t->is($post->Image, null, 'The post has no more media');

$t->is($media1->exists(), true, 'The media still exists');

$t->comment('Reupload the first media to the post');

$form = new DmTestPostForm($post);

$form->bind(array(
  'id'    => $post->id,
  'title' => $post->title,
  'categ_id' => $categ->id,
  'user_id' => $user->id,
  'date' => time(),
  'image_id_form' => array(
    'dm_media_folder_id' => $post->getDmMediaFolder()->get('id')
  )
), array(
  'image_id_form' => array('file' => array(
    'name' => $media1FileName,
    'type' => $helper->get('mime_type_resolver')->getByFilename($media1FullPath),
    'tmp_name' => $media1FullPath,
    'error' => 0,
    'size' => filesize($media1FullPath)
  )))
);

$t->is($form->isValid(), true, 'Form with binded title and image is valid');

$post = $form->save();

$t->is($postId, $post->id, 'The same post has been updated');

$t->is($post->Image->exists(), true, 'The post has an existing media');

$t->isnt($post->Image->id, $media1->id, 'The same old media were NOT reused');
$media1Bis = $post->Image;

$t->is($post->Image->file, $expected = str_replace('.jpg', '_1.jpg', $media1FileName), 'Post media filename is '.$expected);
$t->is($post->Image->size, filesize($media1FullPath), 'Post media size is '.filesize($media1FullPath));

$t->ok($media1->exists(), 'media1 still exists');
$t->is($media1->file, $media1FileName, 'media1 file name is '.$media1FileName);
$t->ok($media1Bis->exists(), 'media1Bis exists');
$t->is($media1Bis->file, $expected = str_replace('.jpg', '_1.jpg', $media1FileName), 'media1Bis file name is '.$expected);

$t->comment('Upload a new media to the post');

$form = new DmTestPostForm($post);

$media2Source   = dmOs::join(sfConfig::get('sf_upload_dir'), 'images/deeper/firefox.jpg');
$media2FileName = 'test_'.dmString::random().'.jpg';
$media2FullPath = sys_get_temp_dir().'/'.$media2FileName;
copy($media2Source, $media2FullPath);
$form->bind(array(
  'id'    => $post->id,
  'title' => $post->title,
  'categ_id' => $categ->id,
  'user_id' => $user->id,
  'date' => time(),
  'image_id_form' => array(
    'id' => $post->Image->id,
    'dm_media_folder_id' => $post->getDmMediaFolder()->get('id')
  )
), array(
  'image_id_form' => array('file' => array(
    'name' => $media2FileName,
    'type' => $helper->get('mime_type_resolver')->getByFilename($media2FullPath),
    'tmp_name' => $media2FullPath,
    'error' => 0,
    'size' => filesize($media2FullPath)
  )))
);

$t->is($form->isValid(), true, 'Form with binded title and new image is valid');

$post = $form->save();

$t->is($postId, $post->id, 'The same post has been updated');

$t->is($post->Image->exists(), true, 'The post has an existing media');

$media2 = $post->Image;
$media2Size = $media2->size;
$t->is($media2->file, $media2FileName, 'Post media filename is '.$media2FileName);
$t->is($media2->size, filesize($media2FullPath), 'Post media size is '.filesize($media2FullPath));

$t->is($media1->exists(), true, 'The first media still exists');

$t->isnt($media1->id, $media2->id, 'The new media is another one');

$t->comment('Upload a new media with the same name');

$form = new DmTestPostForm($post);

$media3Source   = dmOs::join(sfConfig::get('sf_upload_dir'), 'images/default.jpg');
$media3FileName = $media2FileName;
$media3FullPath = sys_get_temp_dir().'/'.dmString::random().$media3FileName;
copy($media3Source, $media3FullPath);
$form->bind(array(
  'id'    => $post->id,
  'title' => $post->title,
  'categ_id' => $categ->id,
  'user_id' => $user->id,
  'date' => time(),
  'image_id_form' => array(
    'id' => $post->Image->id,
    'dm_media_folder_id' => $post->getDmMediaFolder()->get('id')
  )
), array(
  'image_id_form' => array('file' => array(
    'name' => $media3FileName,
    'type' => $helper->get('mime_type_resolver')->getByFilename($media3FullPath),
    'tmp_name' => $media3FullPath,
    'error' => 0,
    'size' => filesize($media3FullPath)
  )))
);

$t->isnt($media2->size, filesize($media3FullPath), 'The uploaded media file has changed');
$t->comment('The uploaded file size is '.filesize($media3FullPath));

$t->is($form->isValid(), true, 'Form with binded title and new image with same name is valid');

$post = $form->save();

$t->is($postId, $post->id, 'The same post has been updated');

$t->is($post->Image->exists(), true, 'The post has an existing media');

$media3 = $post->Image;
$t->is($media3->file, $expected = str_replace('.jpg', '_1.jpg', $media3FileName), 'Post media filename is '.$expected);
$t->is($media3->size, filesize($media3FullPath), 'Post media size is '.filesize($media3FullPath));

$t->isnt($media2->id, $media3->id, 'The new media is NOT the same');

$t->isnt($media2->file, $media3->file, 'The media filename is NOT the same');

$t->isnt($media2Size, $media3->size, 'The media file has changed');

/*
 * clear the mess
 */
$post->delete();
$media1->delete();
$media2->delete();
$media3->delete();
$categ->delete();