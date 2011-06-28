<?php

echo _tag('textarea.dm_code'.($isWritable ? '' : '.dm_readonly'), $textareaOptions, $code);

echo _tag('div.file.clearfix',
  _tag('div.actions.fright',
    ($isWritable ? _tag('a.save', __('Save').' (Ctrl+S)') : '')
  ).
  _tag('span.info'.(!$isWritable ? '.error' : ''), $message)
);

printf('<input type="hidden" value="%s" class="path" />', $path);