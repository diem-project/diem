<?php

echo £o('div.dm.dm_auth.unsupported_browser');

echo £('h1.site_name', dmConfig::get('site_name'));

echo £('div.message',
  £('p.dm_browser_unsupported.mt10', "Sorry, it looks like you're using a browser that isn't supported.").
  £('p.dm_browser_suggestion.mt10', "We suggest that you use one of these browsers:").
  £('div.dm_suggested_browsers.clearfix',
    £link('http://www.mozilla.com/firefox/')->text(£media('dmCore/images/64/firefox.png')->size(64, 64).'Firefox').
    £link('http://www.google.com/chrome')->text(£media('dmCore/images/64/chrome.png')->size(64, 64).'Chrome').
    £link('http://www.apple.com/safari/')->text(£media('dmCore/images/64/safari.png')->size(64, 64).'Safari')
  ).
  £('div.dm_skip_browser_detection',
    £link('+/dmAuth/signin?skip_browser_detection=1')->text(__('Or continue at your own peril'))
  )
);

echo £c('div');

echo £link('http://diem-project.org/')->text('Diem CMF CMS for symfony')->set('.generator_link');