<?php

echo £o('div.dm_gallery_little.clearfix');

  foreach($record->getDmGallery() as $media)
  {
    echo £media($media)->size(40, 40)->set('.media');
  }

echo £c('div');