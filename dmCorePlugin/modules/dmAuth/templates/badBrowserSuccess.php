<style type="text/css">
  iframe {
    width: 100%;
    height: 600px;
  }
</style>
<?php

echo £('h1', __('Sorry, but you can not access administration with your current browser'));

echo £('iframe src='.sprintf('http://www.mozilla-europe.org/%s/firefox', $sf_user->getCulture()));