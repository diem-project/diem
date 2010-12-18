<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot('admin');

$t = new lime_test(16);

dm::loadHelpers(array('Dm'));

$t->is((string)_link('http://c2.com/cgi/wiki?DontRepeatYourself')->text('DRY'), $expected = '<a class="link" href="http://c2.com/cgi/wiki?DontRepeatYourself">DRY</a>', $expected);

$scriptName = $helper->get('request')->getRelativeUrlRoot();
$t->diag('Current cli script name = '.$scriptName);

$expected = $helper->get('controller')->genUrl('@homepage');
$t->is(_link()->getHref(), $expected, 'empty source is '.$expected);
$t->is(_link()->getHref('@homepage'), $expected, 'homepage href is '.$expected);

$expected = $helper->get('controller')->genUrl('dmAuthAdmin/signin');
$t->is(_link('+/dmAuthAdmin/signin')->getHref(), $expected, '+/dmAuthAdmin/signin href is '.$expected);

$expected = $helper->get('controller')->genUrl('dmAuthAdmin/signin');
$t->is($helper->get('helper')->link('+/dmAuthAdmin/signin')->getHref(), $expected, 'with helper service, +/dmAuthAdmin/signin href is '.$expected);

$frontScriptName = $helper->get('script_name_resolver')->get('front');

$t->is(_link('app:front')->getHref(), $frontScriptName, $frontScriptName);

$t->is(_link('app:front/test')->getHref(), $expected = $frontScriptName.'/test', $expected);

$t->is(_link('app:front/test?var1=val1&var2=val2')->getHref(), $expected = $frontScriptName.'/test?var1=val1&var2=val2', $expected);

$t->comment('Create a test page');

$page = dmDb::create('DmPage', array(
  'module'  => dmString::random(),
  'action'  => dmString::random(),
  'name'    => dmString::random(),
  'slug'    => dmString::random()
));
$page->Node->insertAsFirstChildOf(dmDb::table('DmPage')->getTree()->fetchRoot());

$expected = $helper->get('script_name_resolver')->get('front');
$t->is(_link($page)->getHref(), $expected = $frontScriptName.'/'.$page->slug, $expected);
$t->is(_link('page:'.$page->id)->getHref(), $expected = $frontScriptName.'/'.$page->slug, $expected);

$t->is(_link('page:'.$page->id.'?var1=val1&var2=val2')->getHref(), $expected = $frontScriptName.'/'.$page->slug.'?var1=val1&var2=val2', $expected);

$t->is(_link('page:'.$page->id.'?var1=val1&var2=val2#anchor')->getHref(), $expected = $frontScriptName.'/'.$page->slug.'?var1=val1&var2=val2#anchor', $expected);

sfConfig::set('sf_debug', true);

$badSource = 'page:9999999999999';
$errorLink = (string)_link($badSource);
$t->is($errorLink, '<a class="link">'.$badSource.' is not a valid link resource</a>', $errorLink);

$page->Node->delete();

$t->is((string)_link('mailto:test@mail.com')->text('email'), $html = '<a class="link" href="mailto:test@mail.com">email</a>', 'mailto: '.$html);

$t->comment('Nofollow attribute');

$expected = '<a class="link" href="http://site.com" nofollow="1">Site</a>';
$t->is((string)£link('http://site.com')->text('Site')->set('nofollow', true), $expected, '->set("nofollow", true)');

$expected = '<a class="link nofollow" href="http://site.com" nofollow="1">Site</a>';
$t->is((string)£link('http://site.com')->text('Site')->set('.nofollow'), $expected, '->set(".nofollow")');