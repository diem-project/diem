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

function media_file_image_tag($file, $options = array())
{
  $options = array_merge(array(
    'width' => $file->isImage() ? 128 : 64,
    'height' => $file->isImage() ? 98 : 64
  ), dmString::toArray($options, true));

  if ($file->isImage())
  {
    $image = _media($file)->size($options['width'], $options['height']);
  }
  else
  {
    $image = _media('dmCore/images/media/unknown.png')->size($options['width'], $options['height']);
  }

  return $image;
}

function media_file_image_src($file, & $options = array())
{
  $src = null;
  if($file->isImage())
  {
    $src = $file->getThumbnailRelativeUrl(
      aze::getArrayKey($options, 'width', 128),
      aze::getArrayKey($options, 'height', 98)
    );
  }
  else
  {
    switch($file->getType())
    {
      case 'txt':
        $src = '/dmPlugin/images/dm_media/txt.png';
        break;
      case 'xls':
        $src = '/dmPlugin/images/dm_media/xls.png';
        break;
      case 'doc':
        $src = '/dmPlugin/images/dm_media/doc.png';
        break;
      case 'pdf':
        $src = '/dmPlugin/images/dm_media/pdf.png';
        break;
      case 'html':
        $src = '/dmPlugin/images/dm_media/html.png';
        break;
      case 'archive':
        $src = '/dmPlugin/images/dm_media/archive.png';
        break;
      case 'bin':
        $src = '/dmPlugin/images/dm_media/bin.png';
        break;
      default:
        $src = '/dmPlugin/images/dm_media/unknown.png';
    }
  }
  return '../'.$src;
}

function media_wrap_text($text, $distance = 5)
{
  return preg_replace('/([_\-\.])/', '<span class="ws">&nbsp;</span>$1<span class="ws">&nbsp;</span>', $text);
}
