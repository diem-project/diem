<?php

if (!$object || $object->isNew())
{
  return;
}

echo £('div.view',
  //£link($object->getWebPath())->name(
  ($object->isImage()
  ? £media($object)->size(100, 60)
  : £media('dmCore/media/unknown.png')->size(64, 64)
  )
  //)
);