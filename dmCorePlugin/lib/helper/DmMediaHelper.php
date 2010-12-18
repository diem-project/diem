<?php

function media_file_infos(DmMedia $object)
{
  $infos = array(
    __('Size') => dmOs::humanizeSize($object->get('size')),
    __('Type') => $object->get('mime'),
    __('Created at') => format_date($object->get('created_at'), 'f'),
    __('Updated at') => format_date($object->get('updated_at'), 'f'),
    __('Url') => $object->getFullWebPath()
  );
  if ($object->isImage())
  {
    $infos = array_merge(array(
      __('Dimensions') => $object->getDimensions()
    ), $infos);
  }
  return $infos;
}

function media_file_image_tag(DmMedia $file, $options = array())
{
  $options = array_merge(array(
    'width' => $file->isImage() ? 128 : 64,
    'height' => $file->isImage() ? 98 : 64
  ), dmString::toArray($options, true));

  if ($file->isImage())
  {
    $image = _media($file);
  }
  else
  {
    $image = _media('/dmCorePlugin/images/media/unknown.png');
  }

  return $image->size($options['width'], $options['height']);
}

function media_wrap_text($text, $distance = 5)
{
  return preg_replace('/([_\-\.])/', '<span class="ws">&nbsp;</span>$1<span class="ws">&nbsp;</span>', $text);
}
