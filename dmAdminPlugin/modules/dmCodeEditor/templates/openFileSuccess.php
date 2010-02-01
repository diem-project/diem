<?php

$info = _tag("span.info".($isWritable ? '' : '.error'),
  $isWritable ? '' : __('This file is not writable')
);
  
$save = ($isWritable && !$isImage) ? _tag("a.fright.fleft.ml20.sprite_16.sprite_16_save.save", 'Enregistrer') : '';
  
//$delete = $isWritable ? _tag("a.fright.sprite_16.sprite_16_delete.delete", 'Supprimer') : '';
  
echo _tag("div.file_tab.inner",
  _tag("div.inner_border",
    $isImage
    ? _tag("div.image", _media($file)->size(400, 400)->method('scale'))
    : _tag('textarea.dm_code'.($isWritable ? '' : '.dm_readonly'), $textareaOptions, $code)
  ).
  sprintf('<input class="path" type="hidden" value="%s" />', $path).
  _tag("div.action.clearfix",
    $save.
    $info
  )
);