<?php

require_once(realpath(dirname(__FILE__).'/../../../..').'/functional/helper/dmFunctionalTestHelper.php');
$helper = new dmFunctionalTestHelper();
$helper->boot('admin');

$browser = $helper->getBrowser();

$helper->login();

$browser->info('Posts list')
->get('/content/blog/dm-test-posts/index')
->checks(array(
  'moduleAction' => 'dmTestPost/index'
))
->click('tbody td.sf_admin_text:first a')
->checks(array(
  'moduleAction' => 'dmTestPost/edit'
));

$browser->info('Remove all post medias');
$post = dmDb::table('DmTestPost')->find($browser->getRequest()->getParameter('pk'));
$post->removeMedias();

$browser->click('Edit medias')
->checks(array(
  'moduleAction' => 'dmMedia/gallery'
))
->has('.dm_gallery_big ul.list li', false)
->info('Submit invalid form')
->click('form.dm_add_media input.submit', array('dm_gallery_media_form' => array(

)))
->checks(array(
  'moduleAction' => 'dmMedia/gallery',
  'code' => 200,
  'method' => 'post'
))
->has('form.dm_add_media ul.error_list li', 'Required.')
->info('Submit valid form')
->click('form.dm_add_media input.submit', array('dm_media_form' => array(
  'dm_media_folder_id' => $post->getDmMediaFolder()->id,
  'file' => dmOs::join(sfConfig::get('dm_core_dir'), 'test/fixtures/uploads/images/deeper/firefox.jpg')
)))
->checks(array(
  'moduleAction' => 'dmMedia/gallery',
  'code' => 302,
  'method' => 'post'
))
->redirect()
->checks(array(
  'moduleAction' => 'dmMedia/gallery',
  'code' => 200,
  'method' => 'get'
))
->has('.dm_gallery_big ul.list li img')
->info('Remove file')
->click('.dm_gallery_big ul.list li a.delete')
->checks(array(
  'moduleAction' => 'dmMedia/galleryDelete',
  'code' => 302,
  'method' => 'get'
))
->redirect()
->checks(array(
  'moduleAction' => 'dmMedia/gallery',
  'code' => 200,
  'method' => 'get'
))
->has('.dm_gallery_big ul.list li img', false);