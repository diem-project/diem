<style type="text/css">
  iframe {
    width: 100%;
    height: 500px;
  }
</style>
<?php

//echo _tag('iframe.mt10 src='.$sf_request->getAbsoluteUrlRoot().'/dm_check.php');
echo _tag('iframe.mt10 src='._link('dmServer/check')->getHref());

echo _tag('iframe.mt20 src='._link('dmServer/phpinfo')->getHref());
