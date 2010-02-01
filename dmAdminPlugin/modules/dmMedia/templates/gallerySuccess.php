<?php

use_javascript('lib.ui-sortable');
use_stylesheet('admin.gallery');

use_javascript('lib.ui-sortable');
use_javascript('admin.gallery');

echo _open('div.dm_gallery_big', array('json' => $galleryOptions));

echo _tag('div.dm_gallery_actions.clearfix',
  _link($record)->set('.s16.s16_arrow_left.back').
  _tag('a.open_form.dm_big_button', _tag('span.s16.s16_add', __('Add')))
);

echo $form->render('.dm_add_media.dm_form.list.little.ui-corner-all'.($form->isBound() ? '' : '.none').' action="+/dmMedia/gallery?model='.get_class($record).'&pk='.$record->getPrimaryKey().'"');

echo _open('ul.list.clearfix');

foreach($medias as $media)
{
  echo _tag('li#dm_sort_'.$media->get('dm_gallery_rel_id').'.element',
    _media($media)->size(160, 160).
    _link('+/dmMedia/galleryDelete?model='.get_class($record).'&pk='.$record->getPrimaryKey().'&rel_id='.$media->get('dm_gallery_rel_id'))
    ->text(_tag('span.s16block.s16_delete'))
    ->title(__('Remove this media'))
    ->set('.delete.dm_js_confirm')
  );
}

echo _close('ul');

echo _close('div');