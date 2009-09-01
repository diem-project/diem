<?php

if (!$object || !$object->id)
{
	return;
}

use_helper('Date', 'DmMedia');

echo £o('div.dm_media_file');

echo £('h3.title.none', $object->getFile());

echo £o('div.clearfix');

  echo £('div.view',
    $object->isImage()
    ? £media($object)->size(250, 140)
    : £media('dmCore/media/unknown.png')->size(64, 64)
  );

  echo £('div.content',

    £('div.infos',
      definition_list(media_file_infos($object), '.clearfix.dm_little_dl')
    )
  );

echo £c('div');

echo £c('div');