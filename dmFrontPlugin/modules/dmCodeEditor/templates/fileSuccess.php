<?php

echo £('textarea.dm_code'.($isWritable ? '' : '.dm_readonly'), $textareaOptions, $code);

echo £('div.file.clearfix',
  £('div.actions.fright',
    ($isWritable ? £('a.save', __('Save').' (Ctrl+S)') : '')
  ).
  £('span.info'.(!$isWritable ? '.error' : ''), $message)
);

printf('<input type="hidden" value="%s" class="path" />', $path);