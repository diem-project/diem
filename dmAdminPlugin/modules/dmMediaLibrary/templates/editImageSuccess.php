<?php use_helper('sfPixlr');

slot('dm.breadCrumb');

$parents = array();

foreach($file->Folder->getPath() as $parent)
{
  if (!$parent->isRoot())
  {
    $parents[] = £('li', £link(dmMediaTools::getAdminUrlFor($parent))->name($parent->getName()));
  }
}

$parents[] = £('li', £link($sf_request->getUri())->name($file->getFile()));

$bread = implode("", $parents);

echo $bread;

end_slot();

//echo £o('div.dm_box.big');
//
//echo £('h1.title', __('Edit image').' '.$file->getFile());


//dmDebug::show(urldecode(pixlr_express_post_url($absoluteWebUrl, $target, $options)));
//$options['skip_default'] = false;
//dmDebug::kill(urldecode(pixlr_express_post_url($absoluteWebUrl, $target, $options)));
echo sprintf(
  '<iframe class="dm_media pixlr full_height loader" src="%s" width="%s" height="%s" style="border: 0;">%s</iframe>',
  dmOs::isLocalhost()
  ? pixlr_express_post_url($absoluteServerUrl, $target, $options)
  : pixlr_express_get_url($absoluteWebUrl, $target, $options),
  '100%',
  '500px',
  __('Loading...')
);

//echo £('div.dm_box_inner', sprintf(
//  '<form class="pixlr_form" action="%s"></form>',
//  pixlr_post_url($absoluteServerUrl, $target, $options)
//));

//echo £c('div');