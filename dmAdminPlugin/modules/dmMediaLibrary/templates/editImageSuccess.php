<?php

//echo _open('div.dm_box.big');
//
//echo _tag('h1.title', __('Edit image').' '.$file->getFile());


//dmDebug::show(urldecode(pixlr_express_post_url($absoluteWebUrl, $target, $options)));
//$options['skip_default'] = false;
//dmDebug::kill(urldecode(pixlr_express_post_url($absoluteWebUrl, $target, $options)));
echo sprintf(
  '<iframe class="dm_media pixlr full_height loader" src="%s" width="%s" height="%s" style="border: 0;">%s</iframe>',
  $iframeSrc,
  '100%',
  '500px',
  __('Loading...')
);

//echo _tag('div.dm_box_inner', sprintf(
//  '<form class="pixlr_form" action="%s"></form>',
//  pixlr_post_url($absoluteServerUrl, $target, $options)
//));

//echo _close('div');