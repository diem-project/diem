<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot();

$t = new lime_test(61);

$markdown = $helper->get('markdown');
dm::loadHelpers(array('Dm'));

//echo $markdown->toHtml('![image with id, classes and size](%source% #an_id.a_class.another_class 300x200)');die;

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

$tests = array(
  '![basic image](%source%)' => array(    // source
    '',                                   // ->toText
    'basic image',                        // ->brutalToText
    £media($media)->alt('basic image')    // ->toHtml
  ),
  '![image with id](%source% #an_id)' => array(
    '',
    'image with id',
    £media($media)->alt('image with id')->set('#an_id')
  ),
  '![image with classes](%source% .a_class.another_class)' => array(
    '',
    'image with classes',
    £media($media)->alt('image with classes')->set('.a_class.another_class')
  ),
  '![image with id and classes](%source% #an_id.a_class.another_class)' => array(
    '',
    'image with id and classes',
    £media($media)->alt('image with id and classes')->set('#an_id.a_class.another_class')
  ),
  '![image with size](%source% 300x200)' => array(
    '',
    'image with size',
    £media($media)->alt('image with size')->size(300, 200)
  ),
  '![image with square size](%source% 300)' => array(
    '',
    'image with square size',
    £media($media)->alt('image with square size')->size(300, 300)
  ),
  '![image with width](%source% 300x)' => array(
    '',
    'image with width',
    £media($media)->alt('image with width')->width(300)
  ),
  '![image with height](%source% x200)' => array(
    '',
    'image with height',
    £media($media)->alt('image with height')->height(200)
  ),
  '![image with id, classes and size](%source% #an_id.a_class.another_class 300x200)' => array(
    '',
    'image with id, classes and size',
    £media($media)->alt('image with id, classes and size')->set('#an_id.a_class.another_class')->size(300, 200)
  ),
  'a ![basic image](%source%) and a ![image with id, classes and size](%source% #an_id.a_class.another_class 300x200)' => array(
    'a  and a ',
    'a basic image and a image with id, classes and size',
    sprintf('a %s and a %s',
      £media($media)->alt('basic image'),
      £media($media)->alt('image with id, classes and size')->set('#an_id.a_class.another_class')->size(300, 200)
    )
  )
);

$relativeUrlRoot = $helper->get('request')->getRelativeUrlRoot();

foreach($tests as $code => $results)
{
  $sourceId = str_replace('%source%', 'media:'.$media->id, $code);
  $t->comment($sourceId);
  
  $t->is($result = $markdown->toText($sourceId), $results[0], '->toText() '.$result);
  $t->is($result = $markdown->brutalToText($sourceId), $results[1], '->brutalToText() '.$result);
  
  $t->is($result = $markdown->toHtml($sourceId), £('p.dm_first_p', $results[2]), '->toHtml() '.$result);
  
  $sourcePath = str_replace('%source%', '/'.$media->webPath, $code);
  $t->comment($sourcePath);
  
  $t->is($result = $markdown->toText($sourcePath), $results[0], '->toText() '.$result);
  $t->is($result = $markdown->brutalToText($sourcePath), $results[1], '->brutalToText() '.$result);
  
  $resultExpr = preg_replace('|src=".+"|', 'src="__SRC__"', £('p.dm_first_p', $results[2]));
  $resultExpr = str_replace('__SRC__', '.+', '|'.preg_quote($resultExpr, '|').'|');
  $t->like($result = $markdown->toHtml($sourceId), $resultExpr, '->toHtml() '.$result);
}

$media->delete();