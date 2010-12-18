<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot();

$t = new lime_test(5);

$t->is(
  dmOs::join("home"),
  "/home",
  'dmOs::join /home'
);

$t->is(
  dmOs::join(sfConfig::get("sf_root_dir"), "/cache/"),
  sfConfig::get("sf_root_dir")."/cache",
  'dmOs::join sfConfig::get("sf_root_dir"), "/cache/"'
);

$t->is(
  dmOs::join("home", "user/dir", "file.ext"),
  "/home/user/dir/file.ext",
  'dmOs::join "home", "user/dir", "file.ext"'
);

$t->is(
  dmOs::join("//home///user//dir/file.ext///"),
  "/home/user/dir/file.ext",
  'dmOs::join "//home///user//dir/file.ext///"'
);

$t->is(
  dmOs::sanitizeDirName('A strange DIR name é & / \ ( $'),
  $expected = 'A-strange-DIR-name-e',
  'dmOs::sanitizeDirName() : '.$expected
);