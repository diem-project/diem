<style type="text/css">
  iframe {
    width: 100%;
    height: 500px;
  }
</style>
<?php

echo £('iframe.mt10 src='.$sf_request->getAbsoluteUrlRoot().'/dm_check.php');

echo £('iframe.mt20 src='.£link('dmServer/phpinfo')->getHref());