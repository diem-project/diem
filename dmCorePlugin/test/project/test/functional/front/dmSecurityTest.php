<?php

require_once(realpath(dirname(__FILE__).'/../../../..').'/functional/helper/dmFunctionalTestHelper.php');
$helper = new dmFunctionalTestHelper();
$helper->boot('front');

$b = $helper->getBrowser();

// page 11 requires auth
$page11 = dmDb::table('DmPage')->findOneByModuleAndAction('main', 'page11');
$page11->is_secure = true;
$page11->save();

// page 12 requires auth and "word" permission
$page12 = dmDb::table('DmPage')->findOneByModuleAndAction('main', 'page12');
$page12->set('is_secure', true);
$page12->set('credentials', 'word');
$page12->save();

// page 111 requires not auth and "word" permission
$page111 = dmDb::table('DmPage')->findOneByModuleAndAction('main', 'page111');
$page111->set('is_secure', false);
$page111->set('credentials', 'word');
$page111->save();

// page 2 is not active
$page2 = dmDb::table('DmPage')->findOneByModuleAndAction('main', 'page2');
$page2->is_active = false;
$page2->save();

$b
->isAuthenticated(false)
->info('Go to signin page')
->get('security/signin')
->checks(array(
  'code' => 200
))
->isPageModuleAction('main/signin')
->has('.dm_signin_form input.submit')
->info('Go to unsecured page')
->get('page1')
->checks(array(
  'code' => 200
))
->has('.dm_signin_form input.submit', false)
->info('Go to inactive page')
->get('page2')
->checks(array(
  'code' => 404
))
->info('Go to unsecured page with credentials')
->get('page111')
->isPageModuleAction('main/page111')
->checks(array(
  'code' => 200
))
->has('.dm_signin_form input.submit', false)
->info('Go to secured page with credentials')
->get('page12')
->isPageModuleAction('main/signin')
->checks(array(
  'code' => 401
))
->has('.dm_signin_form input.submit')
->info('Go to secured page without credentials')
->get('/page11')
->isPageModuleAction('main/signin')
->checks(array(
  'code' => 401
))
->has('.dm_signin_form input.submit')
->has('.dm_signin_form ul.error_list', false)
->isAuthenticated(false)
->info('Try to signin with empty fields')
->click('Signin', array('signin' => array(), array('_with_csrf' => true)))
->checks(array(
  'moduleAction' => 'dmFront/page',
  'method' => 'post',
  'code' => 200
))
->isPageModuleAction('main/signin')
->has('.dm_signin_form ul.error_list li', 'Required.')
->isAuthenticated(false)
->info('Try to signin with bad username')
->click('Signin', array('signin' => array(
  'username' => 'Marcel',
  'password' => 'marcel'
), array('_with_csrf' => true)))
->checks(array(
  'moduleAction' => 'dmFront/page',
  'method' => 'post',
  'code' => 200
))
->isPageModuleAction('main/signin')
->has('.dm_signin_form ul.error_list li', 'The username and/or password is invalid.')
->isAuthenticated(false)
->info('Signin user admin')
->click('Signin', array('signin' => array(
  'username' => 'admin',
  'password' => 'admin'
), array('_with_csrf' => true)))
->checks(array(
  'moduleAction' => 'dmFront/page',
  'method' => 'post',
  'code' => 302
))
->isPageModuleAction('main/signin')
->redirect()
->checks(array(
  'moduleAction' => 'dmFront/page',
  'method' => 'get',
  'code' => 200
))
->isPageModuleAction('main/root')
->isAuthenticated(true)
->has('.dm_signin_form input.submit', false)
->info('Go to inactive page')
->get('page2')
->checks(array(
  'code' => 200
))
->has('.dm_signin_form input.submit', false)
->info('Go to secured page with credentials')
->get('/page12')
->isPageModuleAction('main/page12')
->checks(array(
  'code' => 200
))
->has('.dm_signin_form input.submit', false)
->info('Signout')
->get('security/signout')
->checks(array(
  'moduleAction' => 'dmUser/signout',
  'method' => 'get',
  'code' => 302
))
->redirect()
->isAuthenticated(false)
->isPageModuleAction('main/signin')
->checks(array(
  'code' => 401
))
->info('Go to secured page without credentials')
->get('/page11')
->info('Try to register with empty fields')
->click('Register', array('dm_user_form' => array(), array('_with_csrf' => true)))
->checks(array(
  'moduleAction' => 'dmFront/page',
  'method' => 'post',
  'code' => 401
))
->isPageModuleAction('main/signin')
->has('.dm_user_form ul.error_list li', 'Required.')
->isAuthenticated(false)
->info('Register user Jannis')
->click('Register', array('dm_user_form' => array(
  'username' => 'Jannis',
  'password' => 'j',
  'password_again' => 'j',
  'email' => 'jannis@nomail.com'
), array('_with_csrf' => true)))
->checks(array(
  'moduleAction' => 'dmFront/page',
  'method' => 'post',
  'code' => 302
))
->isPageModuleAction('main/signin')
->redirect()
->checks(array(
  'moduleAction' => 'dmFront/page',
  'method' => 'get',
  'code' => 200
))
->isPageModuleAction('main/page11')
->isAuthenticated(true)
->has('.dm_signin_form input.submit', false)

->info('Go to secured page with credentials')
->get('page12')
->isPageModuleAction('main/signin');

//move register form out of main/signin to enable these tests
//->checks(array(
//  'code' => 403
//))
//->info('Go to inactive page')
//->get('page2')
//->checks(array(
//  'code' => 403
//))
//->has('.dm_signin_form input.submit')
//->get('/authors')
//->isPageModuleAction('dmUser/list')
//->has('.dm_user_list li a', 'admin')
//->click('.dm_user_list li a')
//->checks()
//->isPageModuleAction('dmUser/show')
//->has('h1', 'admin')
//->has('span.email', 'admin@project.com');


/*
 * With some old version of sqlite, like on continuous integration server
 * This test will not work as expected
 */

if(strpos(getcwd(), 'hudson'))
{
  return;
}

$b->info('Synchronise pages...');
$helper->getService('filesystem')->sf('dm:sync-pages');

$b
->get('/authors')
->click('Jannis')
->checks()
->get('/authors/jannis')
->isPageModuleAction('dmUser/show')
->has('h1', 'Jannis')
->has('span.email', 'jannis@nomail.com');