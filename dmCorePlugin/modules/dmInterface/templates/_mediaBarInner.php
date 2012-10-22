<?php use_helper('DmMedia');

$parents = array();

if (!$folder->getNode()->isRoot())
{
  foreach($folder->getNode()->getAncestors() as $ancestor)
  {
    $parents[] = _tag("a#dmf".$ancestor->get('id'), $ancestor->get('name'));
  }
}

$parents[] = _tag("a#dmf".$folder->get('id'), $folder->get('name'));

echo _tag('div.breadCrumb', implode(" &raquo; ", $parents));

echo _open("ul.content.clearfix");

if ($folder->getNode()->hasParent())
{
  echo _tag("li.folder#dmf".$folder->getNode()->getParent()->get('id'), _media('dmCore/images/media/up.png')->size(64, 64));
}
else
{
  echo _tag('li', _media('dmCore/images/media/up2.png')->size(64, 64));
}

if ($folders = $folder->getNode()->getChildren())
{
  $arrFolders = array();
  foreach($folders as $f) {
    $arrFolders[$f->getName()] = $f;
  }

  ksort($arrFolders);

  foreach($arrFolders as $f)
  {
    echo _tag("li.folder#dmf".$f->get('id'),
      ($f->isWritable() ? _media('dmCore/images/media/folder.png')->size(64, 64)
      : _media('dmCore/images/media/folder-locked.png')->size(64, 64)).
      _tag('span.name', media_wrap_text($f->get('name')))
    );
  }
}

foreach($folder->getMedias() as $f)
{
  echo _open('li.file#dmm'.$f->get('id').'.'.$f->getMimeGroup());
  
  if($f->isImage())
  {
    echo _tag('span.image_background',
      array('style' => sprintf(
        'background: url("%s") top left no-repeat',
        _media($f)->size(128, 128)->quality(80)->getSrc(false)
      )),
      _tag("span.name", media_wrap_text(dmString::truncate($f->get('file'), 40)))
    );
  }
  else
  {
    echo media_file_image_tag($f).
    _tag("span.name", media_wrap_text(dmString::truncate($f->get('file'), 40)));
  }
  echo _close('li');
}

echo _close("ul");