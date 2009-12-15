<?php

require_once(getcwd().'/test/bootstrap/unit.php');

$h = new lime_harness(new lime_output_color());
$h->base_dir = realpath(dirname(__FILE__).'/../test');
$h->register(sfFinder::type('file')->name('*Test.php')->in($h->base_dir));
$h->run();
