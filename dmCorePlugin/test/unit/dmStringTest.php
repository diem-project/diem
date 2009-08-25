<?php

require_once(dmOs::join(sfConfig::get("sf_root_dir"), 'test/bootstrap/unit.php'));

$t = new lime_test(2, new lime_output_color());

$t->ok(
  dmString::slugify(" phrâse avèque dés accënts "),
  "phrase-avec-des-accents"
);

$t->ok(
  dmString::slugify("fonctionnalité"),
  "fonctionnalite"
);