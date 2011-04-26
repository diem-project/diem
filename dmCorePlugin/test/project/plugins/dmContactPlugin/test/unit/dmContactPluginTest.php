<?php

require_once realpath(dirname(__FILE__).'/../../../../..') . '/unit/helper/dmUnitTestHelper.php';

$helper = new dmUnitTestHelper();

$helper->boot();

$t = new lime_test(4);

$module = $helper->get('module_manager')->getModule('dmContact');

$t->ok($module->isProject(), 'project module');

$t->ok($module->isPlugin(), 'plugin module');

$t->is($module->getPluginName(), 'dmContactPlugin', 'dmContact plugin is dmContactPlugin');

$t->ok(!$module->isOverridden(), 'dmContactPlugin is not overridden');