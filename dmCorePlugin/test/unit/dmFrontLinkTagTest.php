<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot('front');

$t = new lime_test(47);

dm::loadHelpers(array('Dm'));

sfConfig::set('sf_no_script_name', false);

dmDb::table('DmPage')->checkBasicPages();

$sc = $helper->get('service_container');

$sc->mergeParameter('link_tag_record.options', array(
  'current_span'=> false
))->mergeParameter('link_tag_page.options', array(
  'current_span'=> false
));
$t->diag('link current_span is false');

$home = dmDb::table('DmPage')->getTree()->fetchRoot();
$helper->get('context')->setPage($home);
$t->diag($home->name.' is the current page');

$testPage = dmDb::create('DmPage', array(
  'module' => 'main',
  'action' => 'test'.dmString::random(12),
  'name'   => 'I am a root child',
  'slug'   => dmString::random()
));

$testPage->Node->insertAsLastChildOf($home);

$scriptName = $helper->get('request')->getScriptName();
$t->diag('Current cli script name = '.$scriptName);

$t->is((string)_link('http://c2.com/cgi/wiki?DontRepeatYourself')->text('DRY'), $expected = '<a class="link" href="http://c2.com/cgi/wiki?DontRepeatYourself">DRY</a>', $expected);

$t->like(£link()->render(), '|<a class="link dm_current|', '£link() has class dm_current');

$t->like(£link()->set('current_class', 'my_current')->render(), '|<a class="link my_current|', '£link() has class my_current');

$t->is((string)£link($home), (string)£link(), '£link($home) is £link()');

$t->is((string)£link('@homepage'), (string)£link($home), '£link("@homepage") is £link()');

$t->is((string)£link('main/root'), (string)£link($home), '£link("main/root") is £link()');

$t->is(£link()->getHref(), $scriptName, 'root href is '.$scriptName);

$t->is(£link()->getText(), $home->name, 'root link text is '.$home->name);

$expected = $helper->get('controller')->genUrl('dmAuth/signin');
$t->is(£link('+/dmAuth/signin')->getHref(), $expected, '+/dmAuth/signin href is '.$expected);

$rootLink = sprintf('<a class="%s" href="%s">%s</a>', 'link dm_current', $scriptName, $home->name);
$t->is((string)£link(), $rootLink, 'root link is '.$rootLink);

$rootLink = sprintf('<a class="%s" href="%s">%s</a>', 'link dm_current', $scriptName, $home->name);
$t->is((string)$helper->get('helper')->link(), $rootLink, 'use the helper service : root link is '.$rootLink);

$hrefWithParam = $scriptName.'?var=val&other=value';
$t->is((string)£link()->param('var', 'val')->param('other', 'value')->getHref(), $hrefWithParam, $hrefWithParam);
$t->is((string)£link()->params(array('var' => 'val', 'other' => 'value'))->getHref(), $hrefWithParam, $hrefWithParam);

$absoluteHrefWithParam = £link()->getAbsoluteHref().'?var=val';
$t->is((string)£link()->param('var', 'val')->getAbsoluteHref(), $absoluteHrefWithParam, $absoluteHrefWithParam);

$absoluteHrefWithParam2 = $absoluteHrefWithParam.'&var2=val2';
$t->is((string)£link($absoluteHrefWithParam)->param('var2', 'val2')->getHref(), $absoluteHrefWithParam2, $absoluteHrefWithParam2);

$absoluteHrefWithParam3 = $absoluteHrefWithParam.'&var2=changed_value';
$t->is((string)£link($absoluteHrefWithParam)->param('var2', 'changed_value')->getHref(), $absoluteHrefWithParam3, $absoluteHrefWithParam3);

$linkWithParam2 = sprintf('<a class="%s" href="%s">%s</a>', 'link', str_replace('&', '&amp;', $absoluteHrefWithParam2), 'abs link with params');
$t->is((string)£link($absoluteHrefWithParam2)->text('abs link with params'), $linkWithParam2, $linkWithParam2);

$testPageLink = sprintf('<a class="%s" href="%s">%s</a>', 'link', $scriptName.'/'.$testPage->slug, $testPage->name);
$t->is((string)£link($testPage), $testPageLink, 'page link is '.$testPageLink);

$testPageLink = sprintf('<a class="%s" href="%s">%s</a>', 'link', $scriptName.'/'.$testPage->slug, $testPage->name);
$t->is((string)$helper->get('helper')->link($testPage), $testPageLink, 'with helper service, page link is '.$testPageLink);

$helper->get('context')->setPage($testPage);

$t->diag($testPage->name.' is the current page');

$t->like(£link($testPage)->render(), '|<a class="link dm_current|', '£link($testPage) has class dm_current');

$t->like(£link($testPage)->set('current_class', 'my_current')->render(), '|<a class="link my_current|', '£link($testPage) has class my_current');

$t->like(£link()->render(), '|<a class="link dm_parent|', '£link() has class dm_parent');

$t->like(£link()->set('parent_class', 'my_parent')->render(), '|<a class="link my_parent|', '£link() has class my_parent');

$testPageLink = sprintf('<a class="%s" href="%s">%s</a>', 'link dm_current', $scriptName.'/'.$testPage->slug, $testPage->name);
$t->is((string)£link($testPage), $testPageLink, 'page link is '.$testPageLink);

$rootLink = sprintf('<a class="%s" href="%s">%s</a>', 'link dm_parent', $scriptName, $home->name);
$t->is((string)£link(), $rootLink, 'root link is '.$rootLink);

$sc->mergeParameter('link_tag_record.options', array(
  'current_span'=> true
))->mergeParameter('link_tag_page.options', array(
  'current_span'=> true
));
$t->diag('link current_span is true');

$testPageLink = sprintf('<span class="%s">%s</span>', 'link dm_current', $testPage->name);
$t->is((string)£link($testPage), $testPageLink, 'page link is '.$testPageLink);

$testPageLink = sprintf('<span class="%s">%s</span>', 'link dm_current', $testPage->name);
$t->is((string)£link('page:'.$testPage->id), $testPageLink, 'page:'.$testPage->id.' link is '.$testPageLink);

$testPage->Node->delete();

//$t->diag('Switch app');
//
//$adminUrl = 'http://symfony/admin.php';
//$t->is(£link('app:admin')->getHref(), $adminUrl, $adminUrl);
//
//$adminUrl2 = 'http://symfony/admin.php/main/test';
//$t->is(£link('app:admin/main/test')->getHref(), $adminUrl2, $adminUrl2);
//
//$adminUrl3 = 'http://symfony/admin.php?var1=val2';
//$t->is(£link('app:admin?var1=val2')->getHref(), $adminUrl3, $adminUrl3);

$t->diag('blank');

$blankLink = sprintf('<a class="link" href="%s" target="%s">%s</a>', 'http://diem-project.org', '_blank', 'http://diem-project.org');
$t->is((string)£link('http://diem-project.org')->target('blank'), $blankLink, 'blank link is '.$blankLink);

$blankLink = sprintf('<a class="link" href="%s">%s</a>', 'http://diem-project.org', 'http://diem-project.org');
$t->is((string)£link('http://diem-project.org')->target('blank')->target(false), $blankLink, 'canceled blank link is '.$blankLink);

$t->diag('media links');
dmDb::table('DmMediaFolder')->checkRoot();
$t->comment('Create a test image media');

$mediaFileName = 'test_image_'.dmString::random().'.jpg';
copy(
  dmOs::join(sfConfig::get('dm_core_dir'), 'data/image/defaultMedia.jpg'),
  dmOs::join(sfConfig::get('sf_upload_dir'), $mediaFileName)
);
$media = dmDb::create('DmMedia', array(
  'file' => $mediaFileName,
  'dm_media_folder_id' => dmDb::table('DmMediaFolder')->checkRoot()->id
))->saveGet();

$t->ok($media->exists(), 'A test media has been created');

$mediaLink = sprintf('<a class="link" href="%s">%s</a>', $helper->get('request')->getAbsoluteUrlRoot().'/'.$media->webPath, $media->file);
$t->is((string)£link($media), $mediaLink, '$media -> '.$mediaLink);
$t->is((string)£link('media:'.$media->id), $mediaLink, 'media:'.$media->id.' -> '.$mediaLink);
$t->is((string)£link('/'.$media->webPath)->text($media->file), $expected = str_replace($helper->get('request')->getAbsoluteUrlRoot(), '', $mediaLink), $media->webPath.' -> '.$expected);

sfConfig::set('sf_debug', true);

$badSource = dmString::random().'/'.dmString::random();
$errorText = '[EXCEPTION] '.$badSource.' is not a valid link resource';
$expr = '_^<a class="link" href="\?dm\_debug=1" title="[^"]+">'.preg_quote($errorText, '_').'</a>$_';
$errorLink = (string)£link($badSource);
$t->like($errorLink, $expr, $errorLink);

sfConfig::set('sf_debug', false);

$badSource = dmString::random().'/'.dmString::random();
$errorLink = '<a class="link"></a>';
$t->is($errorLink, $errorLink, $errorLink);

$media->delete();

$t->is((string)£link('mailto:test@mail.com')->text('email'), $html = '<a class="link" href="mailto:test@mail.com">email</a>', 'mailto: '.$html);

$t->comment('Test use_beaf');
$helper->getServiceContainer()->mergeParameter('link_tag_uri.options', array('use_beaf' => true));

$expected = sprintf(
  '<a class="link beafh" href="%s"><span class="beafore"></span><span class="beafin">%s</span><span class="beafter"></span></span></a>',
  'http://diem-project.org', 'http://diem-project.org'
);
$t->is((string)£link('http://diem-project.org')->set('.beafh'), $expected, 'beafh link is '.$expected);

$expected = sprintf(
  '<a class="link beafv" href="%s"><span class="beafore"></span><span class="beafin">%s</span><span class="beafter"></span></span></a>',
  'http://diem-project.org', 'http://diem-project.org'
);
$t->is((string)£link('http://diem-project.org')->set('.beafv'), $expected, 'beafh link is '.$expected);

$expected = sprintf(
  '<a class="link beafh myclass" href="%s"><span class="beafore"></span><span class="beafin">%s</span><span class="beafter"></span></span></a>',
  'http://diem-project.org', 'http://diem-project.org'
);
$t->is((string)£link('http://diem-project.org')->set('.beafh.myclass'), $expected, 'beafh link is '.$expected);

$expected = sprintf(
  '<a class="link beafv myclass" href="%s"><span class="beafore"></span><span class="beafin">%s</span><span class="beafter"></span></span></a>',
  'http://diem-project.org', 'http://diem-project.org'
);
$t->is((string)£link('http://diem-project.org')->set('.beafv.myclass'), $expected, 'beafh link is '.$expected);


$expected = sprintf(
  '<a class="link" href="%s">%s</a>',
  $helper->getContext()->getRequest()->getPathInfoPrefix().'/simple-url',
  'simple route url'
);
$t->is((string)£link('@link_test_route_1')->text('simple route url'), $expected, 'route link is '.$expected);

$expected = sprintf(
  '<a class="link" href="%s">%s</a>',
  $helper->getContext()->getRequest()->getPathInfoPrefix().'/simple-url?var1=value1',
  'simple route url with extra query string'
);
$t->is((string)£link('@link_test_route_1')->param('var1', 'value1')->text('simple route url with extra query string'), $expected, 'route link is '.$expected);
              
$expected = sprintf(
  '<a class="link" href="%s">%s</a>',
  $helper->getContext()->getRequest()->getPathInfoPrefix().'/advanced-parametered-url/value1/value2',
  'advanced parametered route url'
);
$t->is((string)£link('@link_test_route_2')->params(array('var1'=> 'value1', 'var2' => 'value2'))->text('advanced parametered route url'), $expected, 'route link is '.$expected);

$expected = sprintf(
  '<a class="link" href="%s">%s</a>',
  $helper->getContext()->getRequest()->getPathInfoPrefix().'/advanced-parametered-url/value1/value2?var3=value3',
  'advanced parametered route url with extrauery string'
);
$t->is((string)£link('@link_test_route_2')->params(array('var1'=> 'value1', 'var2' => 'value2'))->param('var3', 'value3')->text('advanced parametered route url with extrauery string'), $expected, 'route link is '.$expected);

sfConfig::set('sf_debug', true);
$expected = 'The "/advanced-parametered-url/:var1/:var2" route has some missing mandatory parameters (:var1, :var2).';
$t->is((string)£link('@link_test_route_2')->text('advanced parametered route url'), $expected, 'route link is '.$expected);
sfConfig::set('sf_debug', false);

$t->comment('Nofollow attribute');

$expected = '<a class="link" href="http://site.com" nofollow="1">Site</a>';
$t->is((string)£link('http://site.com')->text('Site')->set('nofollow', true), $expected, '->set("nofollow", true)');

$expected = '<a class="link nofollow" href="http://site.com" nofollow="1">Site</a>';
$t->is((string)£link('http://site.com')->text('Site')->set('.nofollow'), $expected, '->set(".nofollow")');