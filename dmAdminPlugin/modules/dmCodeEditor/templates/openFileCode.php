<?php

$textareaOptions = array('spellcheck' => 'false');

if(!$file['is_writable'])
{
  $textareaOptions['readonly'] = 'true';
  $textareaOptions['class'] = 'readonly';
}
  
echo _tag('div.file_tab.inner',
  _tag('div.inner_border',
    _tag('textarea.dm_code', $textareaOptions, $file['code'])
  ).
  sprintf('<input class="path" type="hidden" value="%s" />', $file['path']).
  _tag('div.dm_code_editor_actions', _tag('div.dm_code_editor_actions_inner.clearfix',
    ($file['is_writable']
    ? _tag('a.fright.ml20.s16.s16_save.save', __('Save').' (Ctrl+S)')
    : ''
    ).
    _tag('span.info.s16.block'.($file['is_writable'] ? '.s16_tick' : '.s16_error'),
       __($file['is_writable'] ? 'This file is writable' : 'This file is not writable')
    )
  ))
);