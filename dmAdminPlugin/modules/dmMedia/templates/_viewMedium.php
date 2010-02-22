<?php

if (!$object || $object->isNew())
{
  return;
}

use_helper('I18N', 'Date', 'DmAdminMedia');

echo _open('div.dm_media_file');

echo _tag('h3.title.none', $object->getFile());

echo _open('div.clearfix');

  echo _tag('div.view',
    $object->isImage()
    ? _media($object)->size(200, 200)
    : media_file_image_tag($object)
  );

  echo _tag('div.content',

    _tag('div.infos',
      definition_list(array(
        __('Size') => dmOs::humanizeSize($object->getSize()),
        __('Type') => $object->getType(),
        __('Created at') => format_date($object->get('created_at'), 'f'),
        __('Updated at') => format_date($object->get('created_at'), 'f'),
        __('Url') => $object->getFullWebPath()
      ), '.clearfix.dm_little_dl')
    )
  );

echo _close('div');

echo _close('div');