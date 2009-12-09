<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot();

$t = new lime_test(4);

$t->ok(
  dmOs::join("home"),
  "/home"
);

$t->ok(
  dmOs::join(sfConfig::get("sf_root_dir"), "/cache/"),
  sfConfig::get("sf_root_dir")."/cache"
);

$t->ok(
  dmOs::join("home", "user/dir", "file.ext"),
  "/home/user/dir/file.ext"
);

$t->ok(
  dmOs::join("//home///user//dir/file.ext///"),
  "/home/user/dir/file.ext"
);