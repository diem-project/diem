<?php

echo _open('div.dm_gallery_little.clearfix');

  foreach($record->getDmGallery() as $media)
  {
    echo _media($media)->size(40, 40)->set('.media');
  }

echo _close('div');