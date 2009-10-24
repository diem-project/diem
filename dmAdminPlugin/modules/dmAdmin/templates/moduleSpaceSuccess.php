<?php

echo £o('div.dm_module_space_show.mt10');

  echo £o('ul.dm_modules');

  foreach(dmArray::get($menu->getSpaceMenu($space), 'menu', array()) as $key => $moduleArray)
  {
    $module = $moduleManager->getModule($key);
    
    $nbRecords = $module->hasModel() ? $module->getTable()->count() : null;
    
    echo £('li.dm_module',
      £link($moduleArray['link'])
      ->set('.dm_big_button')
      ->text(
        $moduleArray['name'].
        £('span.infos',
          $nbRecords ? format_number_choice('[0]no element|[1]1 element|(1,+Inf]%1% elements', array('%1%' => $nbRecords), $nbRecords) : ''
        )
      )
    );
  }

  echo £c('ul');

echo £c('div');