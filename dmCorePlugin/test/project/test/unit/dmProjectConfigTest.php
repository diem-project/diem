<?php

require_once(realpath(dirname(__FILE__).'/../../..').'/unit/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot('admin');

$t = new lime_test(1);

$t->comment('Check config files order');

$configPath = 'config/dm/modules.yml';
$t->is($helper->getConfiguration()->getConfigPaths($configPath), array(
  dmOs::join(sfConfig::get('dm_core_dir'), $configPath),
  dmOs::join(sfConfig::get('dm_admin_dir'), $configPath),
  dmOs::join(sfConfig::get('dm_core_dir'), 'plugins/dmUserPlugin', $configPath),
  dmOs::join(sfConfig::get('sf_plugins_dir'), 'dmTagPlugin', $configPath),
  dmOs::join(sfConfig::get('sf_plugins_dir'), 'dmContactPlugin', $configPath),
  dmOs::join(sfConfig::get('sf_root_dir'), $configPath),
  dmOs::join(sfConfig::get('sf_apps_dir'), 'admin', $configPath)
), 'config files are well ordered');