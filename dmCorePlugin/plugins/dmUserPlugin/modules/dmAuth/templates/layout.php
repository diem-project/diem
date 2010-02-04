<?php

$helper = $sf_context->get('auth_layout_helper');

echo 
$helper->renderDoctype(),
$helper->renderHtmlTag(),

  "\n<head>\n",
    $helper->renderHead(),
  "\n</head>\n",
  
  $helper->renderBodyTag('bg_2'),

    $sf_content,

    $helper->renderJavascriptConfig(),
      
    $helper->renderJavascripts(),
      
  '</body>',
'</html>';