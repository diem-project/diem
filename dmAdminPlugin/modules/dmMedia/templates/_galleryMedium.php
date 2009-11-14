<?php

$link = £link('+/dmMedia/gallery?model='.get_class($record).'&pk='.$record->getPrimaryKey());

echo £o('div.dm_gallery_medium.clearfix');

  foreach($record->getDmGallery() as $media)
  {
    echo $link->text(£media($media)->size(120, 120)->set('.media'));
  }
  
  echo $link
  ->text(__('Edit medias'))
  ->set('.dm_gallery_link.dm_big_button');

echo £c('div');