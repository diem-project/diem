<?php

require_once(realpath(dirname(__FILE__).'/../../../..').'/functional/helper/dmFunctionalTestHelper.php');
$helper = new dmFunctionalTestHelper();
$helper->boot('admin');

$b = $helper->getBrowser();

$helper->login();

$b
->info('Open media library')
->get('/tools/media/media')
->redirect()
->checks(array(
  'code' => 200,
  'moduleAction' => 'dmMediaLibrary/path',
  'h1' => 'root'
))
->has('li.parent_folder a.link', false)
->has('li.folder span.name', 'images')
->has('li.file span.name', 'default . jpg')

->info('Check controls in root')
->has('.control a.new_folder')
->has('.control a.new_file')
->has('.control a.rename_folder', false)
->has('.control a.move_folder', false)
->has('.control a.delete_folder', false)

->info('Open images folder')
->click('li.folder a')
->checks(array(
  'code' => 200,
  'moduleAction' => 'dmMediaLibrary/path',
  'h1' => 'images'
))
->has('li.parent_folder a.link', true)
->has('li.folder span.name', 'deeper')
->has('li.file span.name', 'default . jpg')

->info('Check controls in non-root folder')
->has('.control a.new_folder')
->has('.control a.new_file')
->has('.control a.rename_folder')
->has('.control a.move_folder')
->has('.control a.delete_folder')

->info('Open deeper folder')
->click('li.folder a')
->checks(array(
  'code' => 200,
  'moduleAction' => 'dmMediaLibrary/path',
  'h1' => 'deeper'
))
->has('li.parent_folder a.link', true)
->has('li.file span.name', 'firefox . jpg')

->info('Back to parent folder')
->click('li.parent_folder a.link')
->checks(array(
  'code' => 200,
  'moduleAction' => 'dmMediaLibrary/path',
  'h1' => 'images'
))

->info('Click on the default.jpg image')
->click('li.file a')
->checks(array(
  'code' => 200,
  'moduleAction' => 'dmMediaLibrary/file',
  'h1' => 'default.jpg'
))
->has('div.view img')
->has('form')

->info('Submit unchanged form')
->click('input.submit')
->checks(array(
  'code' => 200,
  'moduleAction' => 'dmMediaLibrary/saveFile',
  'method' => 'post'
))
->has('h1', false)
->has('form', true)
->has('error_list', false)

->info('Change legend, author and license')
->click('input.submit', array('dm_admin_media_form' => array(
  'author' => 'new author',
  'legend' => 'new legend',
  'license' => 'new license',
  'dm_media_folder_id' => 2,
  'id' => 2
)))
->checks(array(
  'code' => 200,
  'moduleAction' => 'dmMediaLibrary/saveFile',
  'method' => 'post'
))
->has('h1', false)
->has('form', true)
->has('error_list', false);

$media = dmDb::table('DmMedia')->findOneByRelPath('images/default.jpg');

$b->test()->isa_ok($media, 'DmMedia', 'images/default.jpg is a DmMedia');
$b->test()->is($media->legend, 'new legend', 'media legend is '.$media->legend);
$b->test()->is($media->author, 'new author', 'media author is '.$media->author);
$b->test()->is($media->license, 'new license', 'media license is '.$media->license);

$deeperFolder = dmDb::table('DmMediaFolder')->findOneByRelPath('images/deeper');

$b->info('Move the file')
->click('input.submit', array('dm_admin_media_form' => array(
  'author' => 'new author',
  'legend' => 'new legend',
  'license' => 'new license',
  'dm_media_folder_id' => $deeperFolder->id,
  'id' => 2
)))
->checks(array(
  'code' => 200,
  'moduleAction' => 'dmMediaLibrary/saveFile',
  'method' => 'post'
))
->has('form', false)
->testResponseContent('/index.php/tools/media/media/path/images/deeper');

$deeperFolder->refresh(true);
$deeperFolder->refreshRelated('Medias');

$b->test()->is($media->relPath, 'images/deeper/default.jpg', 'media relPath is '.$media->relPath);

$b->info('Follow ajax redirection')
->get('/tools/media/media/path/images/deeper')
->checks(array(
  'code' => 200,
  'moduleAction' => 'dmMediaLibrary/path',
  'h1' => 'deeper'
))
->has('.dm_media_library ul.content li.file:first a span', 'default . jpg');

$b->info('Add a folder')
->get('/tools/media/media')
->redirect()
->click('li.folder a')
->has('h1', 'images')
->click('div.control a.new_folder')
->checks(array(
  'code' => 200,
  'moduleAction' => 'dmMediaLibrary/newFolder'
))
->has('form input#dm_admin_new_media_folder_form_name')

->info('Submit unchanged form')
->click('input.submit')
->checks(array(
  'code' => 200,
  'moduleAction' => 'dmMediaLibrary/createFolder',
  'method' => 'post'
))
->has('.error_list li', 'Required.')

->info('Submit form with bad name')
->click('input.submit', array('dm_admin_new_media_folder_form' => array(
  'name' => '/$'
)))
->checks(array(
  'code' => 200,
  'moduleAction' => 'dmMediaLibrary/createFolder',
  'method' => 'post'
))
->has('.error_list li', '"/$" is not a valid directory name.')

->info('Submit form with good name')
->click('input.submit', array('dm_admin_new_media_folder_form' => array(
  'name' => 'test'
)))
->checks(array(
  'code' => 200,
  'moduleAction' => 'dmMediaLibrary/createFolder',
  'method' => 'post'
))
->has('form', false)
->testResponseContent('/index.php/tools/media/media/path/images/test')

->info('Follow ajax redirection')
->get('/tools/media/media/path/images/test')
->checks(array(
  'code' => 200,
  'moduleAction' => 'dmMediaLibrary/path',
  'h1' => 'test'
))
->has('.dm_media_library ul.content li.file', false)

->info('Back to parent')
->click('.parent_folder a')
->has('h1', 'images')
->has('.dm_media_library ul.content li.folder:last a span.name', 'test')

->info('Go to test folder')
->click('.dm_media_library ul.content li.folder:last a')
->has('h1', 'test')

->info('Rename test folder')
->click('div.control a.rename_folder')
->checks(array(
  'code' => 200,
  'moduleAction' => 'dmMediaLibrary/renameFolder'
))
->has('form input#dm_admin_rename_media_folder_form_name')

->info('Submit form with bad name')
->click('input.submit', array('dm_admin_rename_media_folder_form' => array(
  'name' => '/$'
)))
->checks(array(
  'code' => 200,
  'moduleAction' => 'dmMediaLibrary/renameFolder',
  'method' => 'post'
))
->has('.error_list li', '"/$" is not a valid directory name.')

->info('Submit form with good name')
->click('input.submit', array('dm_admin_rename_media_folder_form' => array(
  'name' => 'test2'
)))
->checks(array(
  'code' => 200,
  'moduleAction' => 'dmMediaLibrary/renameFolder',
  'method' => 'post'
))
->has('form', false)
->testResponseContent('/index.php/tools/media/media/path/images/test2')

->info('Follow ajax redirection')
->get('/tools/media/media/path/images/test2')
->checks(array(
  'code' => 200,
  'moduleAction' => 'dmMediaLibrary/path',
  'h1' => 'test2'
));

$folder = dmDb::table('DmMediaFolder')->findOneByRelPath('images/test2');

$b->info('Move test folder')
->click('div.control a.move_folder')
->checks(array(
  'code' => 200,
  'moduleAction' => 'dmMediaLibrary/moveFolder'
))
->has('form select#dm_admin_move_media_folder_form_parent_id')

->info('Submit unchanged form')
->click('input.submit', array('dm_admin_move_media_folder_form' => array(
  'parent_id' => $folder->nodeParentId
)))
->checks(array(
  'code' => 200,
  'moduleAction' => 'dmMediaLibrary/moveFolder',
  'method' => 'post'
))
->has('form', false)
->testResponseContent('/index.php/tools/media/media/path/images/test2')

->info('Follow ajax redirection')
->get('/tools/media/media/path/images/test2')
->checks(array(
  'code' => 200,
  'moduleAction' => 'dmMediaLibrary/path',
  'h1' => 'test2'
))

->info('Move test2 to root')
->click('div.control a.move_folder')
->click('input.submit', array('dm_admin_move_media_folder_form' => array(
  'parent_id' => dmDb::table('DmMediaFolder')->getTree()->fetchRoot()->id
)))
->checks(array(
  'code' => 200,
  'moduleAction' => 'dmMediaLibrary/moveFolder',
  'method' => 'post'
))
->has('form', false)
->testResponseContent('/index.php/tools/media/media/path/test2')

->info('Follow ajax redirection')
->get('/tools/media/media/path/test2')
->checks(array(
  'code' => 200,
  'moduleAction' => 'dmMediaLibrary/path',
  'h1' => 'test2'
))

->info('Move test2 to images')
->click('div.control a.move_folder')
->click('input.submit', array('dm_admin_move_media_folder_form' => array(
  'parent_id' => dmDb::table('DmMediaFolder')->findOneByRelPath('images')->id
)))
->checks(array(
  'code' => 200,
  'moduleAction' => 'dmMediaLibrary/moveFolder',
  'method' => 'post'
))
->has('form', false)
->testResponseContent('/index.php/tools/media/media/path/images/test2')

->info('Follow ajax redirection')
->get('/tools/media/media/path/images/test2')
->checks(array(
  'code' => 200,
  'moduleAction' => 'dmMediaLibrary/path',
  'h1' => 'test2'
))

->info('Delete the folder')
->click('div.control a.delete_folder')
->redirect()
->checks(array(
  'code' => 200,
  'moduleAction' => 'dmMediaLibrary/path',
  'h1' => 'images'
))

->info('Add a file')
->click('div.control a.new_file')
->checks(array(
  'code' => 200,
  'moduleAction' => 'dmMediaLibrary/saveFile'
))
->has('form input#dm_admin_media_form_file')

->info('Submit unchanged form')
->click('input.submit')
->checks(array(
  'code' => 200,
  'moduleAction' => 'dmMediaLibrary/saveFile',
  'method' => 'post'
))
->has('ul.error_list li', 'Required.');

//->info('Submit form with good file')
//->click('input.submit', array('dm_media_form' => array(
//  'file' => dmOs::join(sfConfig::get('sf_uploads_dir'), '/images/deeper/firefox.jpg')
//)))
//->checks(array(
//  'code' => 200,
//  'moduleAction' => 'dmMediaLibrary/saveFile',
//  'method' => 'post'
//))
//->has('form', false)
//->testResponseContent('/index.php/tools/media/media/path/images/test2');