<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot('front');

$t = new lime_test(14);

$user = $helper->get('user');

$t->ok($helper->get('i18n')->cultureExists($user->getCulture()), 'The user\'s culture exists');

$browser = $user->getBrowser();

$t->is($browser->isUnknown(), true, 'Cli browser is unknown');

$t->is($user->isAuthenticated(), false, 'User is not authenticated');

$t->is($user->can('admin'), false, 'User can not admin');

$randomCredential = dmString::random();

$t->is($user->can($randomCredential), false, 'User can not '.$randomCredential);

$t->diag('grant '.$randomCredential.' credential');
$user->addCredential($randomCredential);

$t->is($user->can($randomCredential), false, 'User can still not '.$randomCredential.' because it\'s not authenticated');

$userRecord = dmDb::table('DmUser')->findOne();
$user->signin($userRecord);

$t->is($user->isAuthenticated(), true, 'User is authenticated');

$t->diag('grant '.$randomCredential.' credential');
$user->addCredential($randomCredential);

$t->is($user->can($randomCredential), true, 'Now user can '.$randomCredential);

$user->signout();

$t->is($user->isAuthenticated(), false, 'User is no more authenticated');

$t->is($user->can($randomCredential), false, 'user can no more '.$randomCredential);

// Testing DmUser records

$username = dmString::random(8);

$t->diag('Create user '.$username);

$userRecord = dmDb::create('DmUser', array(
  'username' => $username,
  'password' => 'changeme',
  'email'    => $username.'@diem-project.org'
))->saveGet();

$t->ok($userRecord->exists(), 'User has been created');

$t->isa_ok($userRecord, 'DmUser', 'User is a DmUser');

$t->isnt($userRecord->password, 'changeme', 'Password has been encrypted');

$userRecord->delete();

$t->ok(!$userRecord->exists(), 'User has been deleted');