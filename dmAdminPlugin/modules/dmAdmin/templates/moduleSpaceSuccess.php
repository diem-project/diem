<?php

slot('dm.breadCrumb');

echo £('li', £link($sf_context->getRouting()->getModuleTypeUrl($type))->text(__($type->getPublicName())));

echo £('li', £('h1', __($space->getPublicName())));

end_slot();

echo £o('div.dm_module_space.dm_module_space_show.dm_box.dm_box.little.mt20');

  echo £('h1.title', $type->getPublicName().' : '.__($space->getName()));

  echo £o('ul.dm_modules.dm_box_inner');

  foreach(dmArray::get($menu->getSpaceMenu($space), 'menu', array()) as $key => $moduleArray)
  {
    $module = $moduleManager->getModule($key);
    
    $nbRecords = $module->hasModel() ? $module->getTable()->count() : null;
    
    echo £('li.dm_module',
      £link($moduleArray['link'])->text($moduleArray['name']).
      £('p.infos', $nbRecords ? format_number_choice('[0]no element|[1]1 element|(1,+Inf]%1% elements', array('%1%' => $nbRecords), $nbRecords) : '')
    );
  }

  echo £c('ul');

echo £c('div');