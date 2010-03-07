<?php

if(!$record->exists())
{
  echo _tag('p.help_box', _tag('span.s16.s16_help.block',
    __('Save this %1% to access to the gallery', array(
      '%1%' => dmString::lcfirst(__($record->getDmModule()->getName()))
    ))
  ));
  
  return;
}

$link = _link('+/dmMedia/gallery?model='.get_class($record).'&pk='.$record->getPrimaryKey());

echo _open('div.dm_gallery_medium.clearfix');

  foreach($record->getDmGallery() as $media)
  {
    echo $link->text(_media($media)->size(120, 120)->set('.media'));
  }
  
  echo $link
  ->text(_tag('span.s16.s16_add.block', __('Edit medias')))
  ->set('.dm_gallery_link.dm_medium_button');

echo _close('div');