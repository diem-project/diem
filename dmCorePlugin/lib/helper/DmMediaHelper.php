<?php

function media_file_referers_link(DmMedia $media)
{
  $html = '<ul class="referers">';

  foreach(dmDb::table('DmMedia')->getRelationHolder()->getForeigns() as $foreignRelation)
  {
    foreach($foreignRelation->fetchRelatedFor($media) as $foreign)
    {
      $html .= sprintf('<li class="referer">%s</li>', £link($foreign));
    }
  }

  return $html.'</ul>';
}

function media_file_infos(DmMedia $object)
{
  $infos = array(
        __('Size') => dmOs::humanizeSize($object->getSize()),
        __('Type') => $object->getMime(),
        __('Created at') => dm_datetime($object->getCreatedAt()),
        __('Updated at') => dm_datetime($object->getUpdatedAt()),
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

function media_display_recursive_folders($folders)
{
  $html = "";

  foreach($folders as $folder_id => $children)
  {
    $folder = DmsMediaFolderPeer::retrieveByPk($folder_id);

    $html .= £o("li rel='$folder_id'");

    $html .= £("span.text", $folder->getNom());

    if (is_array($children))
    {
      $html .= £("ul", media_display_recursive_folders($children));
    }

    $html .= £c("li");
  }

  return $html;
}

function media_file_image_tag($file, $options = array())
{
  $options = array_merge(array(
    'width' => $file->isImage() ? 128 : 64,
    'height' => $file->isImage() ? 98 : 64
  ), dmString::toArray($options, true));

  if ($file->isImage())
  {
    $image = £media($file)->size($options['width'], $options['height']);
  }
  else
  {
    $image = £media('dmCore/media/unknown.png')->size($options['width'], $options['height']);
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
