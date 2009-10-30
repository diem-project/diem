<?php

echo £o('div.dm_associations');

  echo £o('ul.list');

  foreach($record->get($alias) as $associationRecord)
  {
    echo £('li',
      £link($associationRecord)
      ->text($associationRecord->__toString())
      ->title(__('Open'))
      ->set('.associated_record.s16right.s16_arrow_up_right_medium')
    );
  }

  echo £c('ul');

echo £c('div');