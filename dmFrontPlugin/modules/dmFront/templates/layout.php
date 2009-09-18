<?php

$timer = dmDebug::timerOrNull('dmFront/templates/layout');

$helper = dmContext::getInstance()->getService('layout_helper');

echo 
$helper->renderDoctype(),
$helper->renderHtmlTag(),

  "\n<head>\n",
    $helper->renderHttpMetas(),
    $helper->renderMetas(),
    $helper->renderStylesheets(),
    $helper->renderBrowserStylesheets(),
    $helper->renderFavicon(),
    $helper->renderIeHtml5Fix(),
  "\n</head>\n",
  
  $helper->renderBodyTag(),
  
    "\n<!-- DM_SITE_START -->\n",
    $sf_content,
    "\n<!-- DM_SITE_END -->\n",
    
    $helper->renderEditBars(),
    
    $helper->renderJavascriptConfig(),
    $helper->renderJavascripts(),
    $helper->renderGoogleAnalytics(),
  
  "\n</body>\n",

"\n</html>";

$timer && $timer->addTime();