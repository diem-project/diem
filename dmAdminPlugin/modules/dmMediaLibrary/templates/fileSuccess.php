<?php use_helper('Date', 'DmMedia');

echo _open('div.dm_media_file');

echo _tag('h1.title.none', $file->file);

echo _open('div.clearfix');

  echo _tag('div.view',
    _link('/'.$file->getWebPath())
    ->text($file->isImage() ? _media($file)->size(300, 300) : media_file_image_tag($file))
    ->target('blank')
  );

  echo _tag('div.content',

    _tag('div.infos',
      definition_list(array(
        __('Size') => dmOs::humanizeSize($file->size),
        __('Type') => $file->mime,
        __('Created at') => format_datetime($file->createdAt),
        __('Updated at') => format_datetime($file->updatedAt),
        __('Url') => $file->getFullWebPath()
      ), '.clearfix').
      _link($file->getFullWebPath())->text(__('Download'))->target('blank')->set('.block.s16.s16_download')
    ).

    get_partial('dmInterface/flash').

    _tag('div.form', $form->render('.dm_form.list.little action=dmMediaLibrary/saveFile')).

    _tag('div.actions.clearfix',
      _tag('a.close_dialog.button.fright', __('Close')).
      _link('dmMediaLibrary/deleteFile?media_id='.$file->id)
      ->text(__('Delete'))
      ->set('.button.red.dm_js_confirm.fleft')
      ->title(__('Delete this file')).
      ((false && $file->isImage())
      ? _link('dmMediaLibrary/editImage?media_id='.$file->id)
      ->text(__('Edit image'))
      ->set('.button.edit_image')
      : '')
    )

  );

echo _close('div');

echo _close('div');