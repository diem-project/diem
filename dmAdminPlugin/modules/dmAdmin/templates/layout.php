<?php

$timer = dmDebug::timer('dmAdmin/templates/layout');

$helper = dmContext::getInstance()->getLayoutHelper();

echo 
$helper->renderDoctype(),
$helper->renderHtmlTag(),

  "\n<head>\n",
    $helper->renderHttpMetas(),
    $helper->renderMetas(),
    $helper->renderStylesheets(),
    $helper->renderFavicon(),
  "\n</head>\n",
  
  $helper->renderBodyTag(),

	  sprintf('<div id="dm_admin_content" class="module_%s action_%s">',
	    $sf_request->getParameter('module'),
	    $sf_request->getParameter('action')
	  ),

	    get_partial('dmAdmin/flash'),
	
	    $sf_content,

  	'</div>',
	      
	  $helper->renderEditBars(),
	   
	  $helper->renderJavascriptConfig(),
	    
	  $helper->renderJavascripts(),
	    
	'</body>',
'</html>';

$timer->addTime();