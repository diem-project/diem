<?php

echo _open('div.dm_auto_seo_manager.ui-tabs.ui-widget.ui-widget-content.ui-corner-all.mt10');

include_partial('dmAutoSeo/tabs', array('autoSeos' => $autoSeos));

echo _tag('h2.mt10.ml20.mb10', 'Efficiently manage SEO for automatic pages');

echo _close('div');