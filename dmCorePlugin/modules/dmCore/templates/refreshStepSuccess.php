<?php

// used only for debugging purpose

echo £('pre', print_r($data, true));

echo £('p', 'mem : '.(memory_get_peak_usage(true) / 1024 / 1024));

echo £('p', 'time : '.(microtime(true) - dm::getStartTime()));