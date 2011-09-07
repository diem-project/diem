<?php use_helper('DmMedia');

echo _open('div.dm_media_library', array('json' => $metadata));

echo _open('div.content_wrap');

echo _open('div.list.clearfix');

echo _open('div.right.dm_box');

echo _tag('h2.title', __('Menu'));

echo _tag('div.control_wrap.dm_box_inner', _tag('div.control', $controlMenu->render()));

echo _close('div'); // right

echo _open('div.center');

echo _open('ul.content.clearfix');

if ($folder->isRoot())
{
  echo _tag('li.parent_folder', _tag('a.root', _media('dmAdmin/images/media/up2.png')->size(64, 64)));
}
else
{
  echo _tag('li.parent_folder',
    _link($sf_context->getRouting()->getMediaUrl($folder->getNode()->getParent()))
    ->text(_media('dmAdmin/images/media/up.png')->size(64, 64)->alt(__('Back to the parent folder')))
  );
}

if ($children = $folder->getNode()->getChildren())
{
  $arrChildren = array();
  foreach($children as $f) {
    $arrChildren[$f->getName()] = $f;
  }

  ksort($arrChildren);

  foreach($arrChildren as $f)
  {
    echo _tag('li.folder',
      _link($sf_context->getRouting()->getMediaUrl($f))->text(
        ($f->isWritable() ? _media('dmAdmin/images/media/folder.png')->size(64, 64)
        : _media('dmAdmin/images/media/folder-locked.png')).
        _tag('span.name', media_wrap_text($f->getName())).
        _tag('span.more', format_number_choice('[0]no element|[1]1 element|(1,+Inf]%1% elements', array('%1%' => $f->getNbElements()), $f->getNbElements()))
      )
    );
  }
}

foreach($files as $f)
{
  echo _tag('li.file.media_id_'.$f->getId(),
    (($f->isImage() && $f->checkFileExists())
    ? _link($sf_context->getRouting()->getMediaUrl($f))->text(
        _tag('span.image_background',
          array('style' => sprintf(
            'background: url("%s") top left no-repeat',
            _media($f)->size(128, 128)->quality(80)->getSrc()
          )),
          _tag('span.name', media_wrap_text(dmString::truncate($f->getFile(), 40)))
        )
      )
    : _link($sf_context->getRouting()->getMediaUrl($f))->text(
        media_file_image_tag($f).
        _tag('span.name', media_wrap_text(dmString::truncate($f->getFile(), 40)))
      )
    )
  );
}

echo _close('ul');

echo _close('div'); // center

echo _close('div'); // list

echo _close('div');

echo _close('div');