<?php

$indices = array();

foreach($engine->getIndices() as $name => $index)
{
  $description = $index->describe();
  $indices[sprintf('%s', ucfirst(format_language($index->getCulture())))] =
    _tag('div.clearfix',
      _tag('span.fleft.s16.s16_file_text style=width:100px', $description['Documents'].' '.__('Pages')).
      _tag('span.fleft', $description['Size'])
    );
}

echo definition_list($indices, '.dm_little_dl');