<?php

$diemRootDir = realpath(dirname(__FILE__));

require_once($diemRootDir.'/dmCorePlugin/lib/dm.php');

dm::register($diemRootDir);