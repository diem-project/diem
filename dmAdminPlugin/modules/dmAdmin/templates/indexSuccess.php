<?php

echo £('h1', dmConfig::get('site_name'));

echo £o('div.clearfix');

include_partial('dmAdmin/userLog', array('userLogView' => $userLogView));

include_partial('dmAdmin/actionLog', array('actionLogView' => $actionLogView));

echo £c('div');