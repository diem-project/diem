<?php

if (!$object || $object->isNew())
{
  return;
}

use_helper('I18N', 'Date', 'DmAdminMedia');

echo £o('div.dm_media_file');

echo £('h3.title.none', $object->getFile());

echo £o('div.clearfix');

  echo £('div.view',
    $object->isImage()
    ? £media($object)->size(200, 200)
    : media_file_image_tag($object)
  );

  echo £('div.content',

    £('div.infos',
      definition_list(array(
        __('Size') => dmOs::humanizeSize($object->getSize()),
        __('Type') => $object->getType(),
        __('Created at') => format_datetime($object->getCreatedAt()),
        __('Updated at') => format_datetime($object->getUpdatedAt()),
        __('Url') => $object->getFullWebPath()
      ), '.clearfix.dm_little_dl')
    )
  );

echo £c('div');

echo £c('div');