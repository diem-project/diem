<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot('admin');

$t = new lime_test(2);

$t->comment('Check config files order');

$configPath = 'config/dm/modules.yml';
$paths = $helper->getConfiguration()->getConfigPaths($configPath);

$t->is($paths[0], dmOs::join(sfConfig::get('dm_core_dir'), $configPath), 'dmCore is first');
$t->is($paths[1], dmOs::join(sfConfig::get('dm_admin_dir'), $configPath), 'dmAdmin is second');