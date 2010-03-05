<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot();

$t = new lime_test(39);

dmDb::table('DmMediaFolder')->checkRoot();
$t->comment('Create a test image media');

$mediaFileName = 'test_'.dmString::random().'.jpg';
copy(
  dmOs::join(sfConfig::get('dm_core_dir'), 'data/image/defaultMedia.jpg'),
  dmOs::join(sfConfig::get('sf_upload_dir'), $mediaFileName)
);
$media = dmDb::create('DmMedia', array(
  'file' => $mediaFileName,
  'dm_media_folder_id' => dmDb::table('DmMediaFolder')->checkRoot()->id
))->saveGet();

$t->ok($media->exists(), 'A test media has been created');

$v = new dmValidatorLinkUrl();

// ->clean()
$t->diag('->clean()');
foreach (array(
  'http://www.google.com',
  'https://google.com/',
  'https://google.com:80/',
  'http://www.symfony-project.com/',
  'http://127.0.0.1/',
  'http://127.0.0.1:80/',
  'ftp://google.com/foo.tgz', 
  'ftps://google.com/foo.tgz',
  'page:'.dmDb::table('DmPage')->findOne()->id,
  'media:'.$media->id,
  'page:'.dmDb::table('DmPage')->findOne()->id.' some text after',
  'media:'.$media->id.' some text after',
  '#',
  '#anchor',
  '@my_route'
) as $url)
{
  try
  {
    $t->is($v->clean($url), $url, '->clean() checks that the value is a valid URL');
  }
  catch (sfValidatorError $e)
  {
    $t->fail('->clean() throws an sfValidatorError: '.$e->getMessage());
  }
}

foreach (array(
  'google.com',
  'http:/google.com',
  'http://google.com::aa',
  'page:a',
  'media:a',
  'page: some text',
  'media: some text',
  'page:0',
  'media:0',
  'page:0 some text after',
  'media:0 some text after'
) as $nonUrl)
{
  try
  {
    $v->clean($nonUrl);
    $t->fail('->clean() throws an sfValidatorError if the value is not a valid URL');
    $t->skip('', 1);
  }
  catch (sfValidatorError $e)
  {
    $t->pass('->clean() throws an sfValidatorError if the value is not a valid URL');
    $t->is($e->getCode(), 'invalid', '->clean() throws a sfValidatorError');
  }
}

$v = new sfValidatorUrl(array('protocols' => array('http', 'https')));
try
{
  $v->clean('ftp://google.com/foo.tgz');
  $t->fail('->clean() only allows protocols specified in the protocols option');
}
catch (sfValidatorError $e)
{
  $t->pass('->clean() only allows protocols specified in the protocols option');
}

$media->delete();