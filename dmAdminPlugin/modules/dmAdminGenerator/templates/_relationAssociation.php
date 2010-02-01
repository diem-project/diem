<?php

echo _open('div.dm_associations');

  echo _open('ul.list');

  foreach($record->get($alias) as $associationRecord)
  {
    echo _tag('li',
      _link($associationRecord)
      ->text($associationRecord->__toString())
      ->title(__('Open'))
      ->set('.associated_record.s16right.s16_arrow_up_right_medium')
    );
  }

  echo _close('ul');

echo _close('div');