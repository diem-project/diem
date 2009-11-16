<?php use_helper('Date', 'DmMedia');

echo £o('div.dm_media_file');

echo £('h1.title.none', $file->file);

echo £o('div.clearfix');

  echo £('div.view',
    £link($file->getWebPath())->text(
    ($file->isImage()
    ? £media($file)->size(300, 300)
    : media_file_image_tag($file)
    ))
  );

  echo £('div.content',

    £('div.infos',
      definition_list(array(
        __('Size') => dmOs::humanizeSize($file->size),
        __('Type') => $file->mime,
        __('Created at') => format_datetime($file->createdAt),
        __('Updated at') => format_datetime($file->updatedAt),
        __('Url') => $file->getFullWebPath(),
        __('Referers') => media_file_referers_link($file)
      ), '.clearfix')
    ).

    get_partial('dmInterface/flash').

    £('div.form', $form->render('.dm_form.list.little action="dmMediaLibrary/saveFile?dm_xhr=1"')).

    £('div.actions.clearfix',
      £('a.close_dialog.button.fright', __('Close')).
      £link('dmMediaLibrary/deleteFile?media_id='.$file->id)
      ->text(__('Delete'))
      ->set('.button.red.dm_js_confirm.fleft')
      ->title(__('Delete this file')).
      ((false && $file->isImage())
      ? £link('dmMediaLibrary/editImage?media_id='.$file->id)
      ->text(__('Edit image'))
      ->set('.button.edit_image')
      : '')
    )

  );

echo £c('div');

echo £c('div');