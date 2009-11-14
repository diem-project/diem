<?php

use_javascript('lib.ui-sortable');
use_stylesheet('admin.gallery');

use_javascript('lib.ui-sortable');
use_javascript('admin.gallery');

echo £o('div.dm_gallery_big', array('json' => $galleryOptions));

echo £('div.dm_gallery_actions.clearfix',
  £link($record)->set('.s16.s16_arrow_left.back').
  £('a.open_form.dm_big_button', £('span.s16.s16_add', __('Add')))
);

echo $form->render('.dm_add_media.dm_form.list.little.ui-corner-all'.($form->isBound() ? '' : '.none').' action="+/dmMedia/gallery?model='.get_class($record).'&pk='.$record->getPrimaryKey().'"');

echo £o('ul.list.clearfix');

foreach($medias as $media)
{
  echo £('li#dm_sort_'.$media->get('dm_gallery_rel_id').'.element',
    £media($media)->size(160, 160).
    £link('+/dmMedia/galleryDelete?model='.get_class($record).'&pk='.$record->getPrimaryKey().'&rel_id='.$media->get('dm_gallery_rel_id'))
    ->text(£('span.s16block.s16_delete'))
    ->title(__('Remove this media'))
    ->set('.delete.dm_js_confirm')
  );
}

echo £c('ul');

echo £c('div');