<?php use_helper("DmMedia", "Form");

echo £('h1', __("Media library"));

echo £o("div.dm_media_library", array("json" => $metadata));

echo £o("div.content_wrap");

slot('dm.breadCrumb');
  include_partial("dmMediaLibrary/breadCrumb", array("folder"=>$folder));
end_slot();

echo £o("div.list.clearfix");

echo £o("div.right.dm_box");

echo £("h2.title", __("Menu"));

echo £("div.control_wrap.dm_box_inner",
  get_partial("dmMediaLibrary/control", array("folder" => $folder))
);

echo £c("div"); // right

echo £o("div.center");

echo £o("ul.content.clearfix");

if ($folder->isRoot())
{
  echo £("li", £("a.root", £media("dmAdmin/images/media/up2.png")->size(64, 64)));
}
else
{
  echo £("li", (£link(dmMediaTools::getAdminUrlFor($folder->getNode()->getParent()))->text(£media('dmAdmin/images/media/up.png')->size(64, 64))));
}

if ($children = $folder->getNode()->getChildren())
{
  foreach($children as $f)
  {
    echo £("li.folder",
      £link(dmMediaTools::getAdminUrlFor($f))->text(
        ($f->isWritable() ? £media("dmAdmin/images/media/folder.png")->size(64, 64)
        : £media("dmAdmin/images/media/folder-locked.png")).
        £("span.name", media_wrap_text($f->getName())).
        £("span.more", format_number_choice('[0]no element|[1]1 element|(1,+Inf]%1% elements', array('%1%' => $f->getNbElements()), $f->getNbElements()))
      )
    );
  }
}

foreach($files as $f)
{
  echo £("li.file.media_id_".$f->getId(),
    ($f->isImage()
    ? £link(dmMediaTools::getAdminUrlFor($f))->text(
        £('span.image_background',
          array('style' => sprintf(
            'background: url(%s) top left no-repeat',
            £media($f)->size(128, 128)->quality(80)->getSrc()
          )),
          £("span.name", media_wrap_text(dmString::truncate($f->getFile(), 40)))
        )
      )
    : £link(dmMediaTools::getAdminUrlFor($f))->text(
        media_file_image_tag($f).
        £("span.name", media_wrap_text(dmString::truncate($f->getFile(), 40)))
      )
    )
  );
}

echo £c("ul");

echo £c("div"); // center

echo £c("div"); // list

echo £c("div");

echo £c("div");