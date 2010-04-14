<?php

echo _open('div.dm.dm_auth.unsupported_browser');

echo _tag('h1.site_name', dmConfig::get('site_name'));

echo _tag('div.message',
  _tag('p.dm_browser_unsupported.mt10', __("Sorry, it looks like you're using a browser that isn't supported.")).
  _tag('p.dm_browser_suggestion.mt10', __("We suggest that you use one of these browsers:")).
  _tag('div.dm_suggested_browsers.clearfix',
    _link('http://www.mozilla.com/firefox/')->text(_media('dmCore/images/64/firefox.png')->size(64, 64).'Firefox').
    _link('http://www.google.com/chrome')->text(_media('dmCore/images/64/chrome.png')->size(64, 64).'Chrome').
    _link('http://www.apple.com/safari/')->text(_media('dmCore/images/64/safari.png')->size(64, 64).'Safari').
    _link('http://www.opera.com/browser/')->text(_media('dmCore/images/64/opera.png')->size(64, 64).'Opera')
  ).
  _tag('div.dm_skip_browser_detection',
    _link('@signin?skip_browser_detection=1')->text(__('Or continue at your own peril'))
  )
);

echo _close('div');

echo _link('http://diem-project.org/')->text('Diem CMF CMS for symfony')->set('.generator_link');