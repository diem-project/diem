<style type="text/css">
  iframe {
    width: 100%;
    height: 500px;
  }
</style>
<?php

echo £o('div.dm_box.big.search_engine');

echo £('h1.title', __('Server'));

echo £o('div.dm_box_inner');

echo £('iframe src='.$sf_request->getAbsoluteUrlRoot().'/dm_check.php');

echo £('iframe.mt20 src='.dmAdminLinkTag::build('dmServer/phpinfo')->getHref());

echo £c('div');

echo £c('div');