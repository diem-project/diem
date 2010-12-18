<?php

use_helper('DmMedia');

echo _open('div.dm_gallery_little.clearfix');

  foreach($record->getDmGallery() as $media)
  {
    echo media_file_image_tag($media, array(
      'width' => 40,
      'height' => 40,
      'class' => 'media'
    ));
  }

echo _close('div');