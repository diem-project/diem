<?php

$info = £("span.info".($isWritable ? '' : '.error'),
  $isWritable ? '' : __('This file is not writable')
);
  
$save = ($isWritable && !$isImage) ? £("a.fright.fleft.ml20.sprite_16.sprite_16_save.save", 'Enregistrer') : '';
  
//$delete = $isWritable ? £("a.fright.sprite_16.sprite_16_delete.delete", 'Supprimer') : '';
  
echo £("div.file_tab.inner",
  £("div.inner_border",
    $isImage
	  ? £("div.image", £media($file)->size(400, 400)->method('scale'))
	  : £('textarea.dm_code'.($isWritable ? '' : '.dm_readonly'), $textareaOptions, $code)
  ).
  sprintf('<input class="path" type="hidden" value="%s" />', $path).
  £("div.action.clearfix",
    $save.
    $info
  )
);