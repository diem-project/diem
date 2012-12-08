<?php

if (!$object || $object->isNew())
{
  return;
}

echo 
//  _link($object->fullWebPath)->text(
  ($object->isImage()
  ? _media($object)->size(100, 60)
  : _media('dmCore/images/media/unknown.png')->size(64, 64)
  )
//  )
;