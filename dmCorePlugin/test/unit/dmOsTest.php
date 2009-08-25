<?php

require_once(dmOs::join(sfConfig::get("sf_root_dir"), 'test/bootstrap/unit.php'));

$t = new lime_test(4, new lime_output_color());

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