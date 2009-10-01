<?php use_helper('DmMedia');

$parents = array();

if (!$folder->getNode()->isRoot())
{
  foreach($folder->getNode()->getAncestors() as $ancestor)
  {
    $parents[] = £("a#dmf".$ancestor->get('id'), $ancestor->get('name'));
  }
}

$parents[] = £("a#dmf".$folder->get('id'), $folder->get('name'));

echo £('div.breadCrumb', implode(" &raquo; ", $parents));

echo £o("ul.content.clearfix");

if ($folder->getNode()->hasParent())
{
  echo £("li.folder#dmf".$folder->getNode()->getParent()->get('id'), £media('dmCore/images/media/up.png')->size(64, 64));
}
else
{
  echo £("li", £media('dmCore/images/media/up2.png')->size(64, 64));
}

if ($folders = $folder->getNode()->getChildren())
{
  foreach($folders as $f)
  {
    echo £("li.folder#dmf".$f->get('id'),
      ($f->isWritable() ? £media('dmCore/images/media/folder.png')->size(64, 64)
      : £media('dmCore/images/media/folder-locked.png')->size(64, 64)).
      £('span.name', media_wrap_text($f->get('name')))
    );
  }
}

foreach($folder->getMedias() as $f)
{
  echo £("li.file#dmm".$f->get('id'),
    ($f->isImage()
    ? £('span.image_background',
        array('style' => sprintf(
          'background: url(%s) top left no-repeat',
          £media($f)->size(128, 128)->quality(80)->getSrc()
        )),
        £("span.name", media_wrap_text(dmString::truncate($f->get('file'), 40)))
      )
    : media_file_image_tag($f).
      £("span.name", media_wrap_text(dmString::truncate($f->get('file'), 40)))
    )
  );
}

echo £c("ul");