<?php

/*
 * Shows server configuration and verify its compatibility with Diem 5.
 * This file must be protected in production server for obvious security reasons.
 * Just uncomment the following line to make it unreachable.
 */

// header('HTTP/1.0 404 Page Not Found'); die;

require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');

dm::checkServer();