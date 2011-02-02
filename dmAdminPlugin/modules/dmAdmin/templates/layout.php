<?php

$helper = $sf_context->get('layout_helper');

echo 
$helper->renderDoctype(),
$helper->renderHtmlTag(),

  "\n<head>\n",
    $helper->renderHead(),
  "\n</head>\n",
  
  $helper->renderBodyTag('.dm'),

    sprintf('<div id="dm_admin_content" class="module_%s action_%s">',
      $sf_request->getParameter('module'),
      $sf_request->getParameter('action')
    ),

      sfConfig::get('dm_admin_embedded') ? '' : $sf_context->get('bread_crumb')->render(),

      get_partial('dmInterface/flash'),
  
      $sf_content,

    '</div>',

    $helper->renderEditBars(),
     
    $helper->renderJavascriptConfig(),
      
    $helper->renderJavascripts(),
      
  '</body>',
'</html>';