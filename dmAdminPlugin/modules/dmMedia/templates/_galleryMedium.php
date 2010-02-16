<?php

$link = _link('+/dmMedia/gallery?model='.get_class($record).'&pk='.$record->getPrimaryKey());

echo _open('div.dm_gallery_medium.clearfix');

  foreach($record->getDmGallery() as $media)
  {
    echo $link->text(_media($media)->size(120, 120)->set('.media'));
  }
  
  echo $link
  ->text(__('Edit medias'))
  ->set('.dm_gallery_link.dm_big_button');

echo _close('div');