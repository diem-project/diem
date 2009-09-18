<?php

$indices = array();

foreach($index->describe() as $culture => $description)
{
  $indices[ucfirst(format_language($culture))] =
    £('div.clearfix',
      £('span.fleft.s16.s16_file_text style=width:100px', $description['Documents'].' '.__('pages')).
      £('span.fleft', $description['Size'])
    );
}

echo definition_list($indices, '.dm_little_dl');