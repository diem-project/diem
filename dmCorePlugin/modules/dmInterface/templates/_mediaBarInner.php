<?php use_helper('DmMedia');

$parents = array();

if (!$folder->getNode()->isRoot())
{
	foreach($folder->getNode()->getAncestors() as $ancestor)
	{
		$parents[] = £("a#dmf".$ancestor->id, $ancestor->name);
	}
}
$parents[] = £("a#dmf".$folder->id, $folder->name);

$bread = implode(" &raquo; ", $parents);

echo £('div.breadCrumb', $bread);

echo £o("ul.content.clearfix");

if ($folder->getNode()->hasParent())
{
  echo £("li.folder#dmf".$folder->Node->getParent()->id, £media('dmCore/media/up.png')->size(64, 64));
}
else
{
  echo £("li", £media("dmCore/media/up2.png")->size(64, 64));
}

if ($folders = $folder->getNode()->getChildren())
{
	foreach($folders as $f)
	{
	  echo £("li.folder#dmf".$f->id,
	    ($f->isWritable() ? £media("dmCore/media/folder.png")->size(64, 64)
	    : £media("dmCore/media/folder-locked.png")->size(64, 64)).
	    £("span.name", media_wrap_text($f->name))
	  );
	}
}

foreach($folder->Medias as $f)
{
  echo £("li.file#dmm".$f->id,
    ($f->isImage()
    ? £('span.image_background',
        array('style' => sprintf(
          'background: url(%s) top left no-repeat',
          £media($f)->size(128, 128)->src()
        )),
        £("span.name", media_wrap_text(dmString::truncate($f->file, 40)))
      )
    : media_file_image_tag($f).
      £("span.name", media_wrap_text(dmString::truncate($f->file, 40)))
    )
  );
}

echo £c("ul");