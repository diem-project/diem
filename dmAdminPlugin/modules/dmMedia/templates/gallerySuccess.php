<?php

use_javascript('lib.ui-sortable');
use_stylesheet('admin.gallery');

use_javascript('lib.ui-sortable');
use_javascript('admin.gallery');

echo _open('div.dm_gallery_big', array('json' => $galleryOptions));

echo $addByIdForm->open('action=dmMedia/addToGalleryById').
$addByIdForm['media_id']->field().
$addByIdForm['model']->field().
$addByIdForm['pk']->field().
$addByIdForm->close();

echo _tag('div.dm_gallery_actions.clearfix',
  _link($record)->set('.s16.s16_arrow_left.back').
  _tag('a.open_form.dm_big_button', _tag('span.s16.s16_add', __('Add')))
);

echo $form->render('.dm_add_media.dm_form.list.little.ui-corner-all'.($form->isBound() ? '' : '.none').' action="+/dmMedia/gallery?model='.get_class($record).'&pk='.$record->getPrimaryKey().'"');
echo _tag('div.help_box', __('Drag & drop a media here'));
echo _open('ul.list.clearfix');

foreach($medias as $media)
{
  try
  {
    $mediaHtml = _media($media)->size(160, 160);
  }
  catch(dmException $e)
  {
    $mediaHtml = _media('/dmCorePlugin/images/media/unknown.png')->size(160, 140).$media->file;
  }
  echo _tag('li#dm_sort_'.$media->get('dm_gallery_rel_id').'.element',
    $mediaHtml.
    _link('+/dmMedia/galleryDelete?model='.get_class($record).'&pk='.$record->getPrimaryKey().'&rel_id='.$media->get('dm_gallery_rel_id'))
    ->text(_tag('span.s16block.s16_delete'))
    ->title(__('Remove this media'))
    ->set('.delete.dm_js_confirm').
    _link($sf_context->getRouting()->getMediaUrl($media))
    ->text(_tag('span.s16block.s16_edit'))
    ->title(__('Edit this media'))
    ->set('.edit')
  );
}

echo _close('ul');

echo _close('div');