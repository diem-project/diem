<?php

echo £o('div.dm_module_space.dm_module_space_show.dm_box.dm_box.little.mt20');

  echo £('h1.title', $type->getPublicName().' : '.__($space->getName()));

  echo £o('ul.dm_modules.dm_box_inner');

  foreach($modules as $module)
  {
    $nbObjects = $module->hasModel() ? $module->getTable()->count() : null;
    echo £('li.dm_module',
      £link('@'.$module->getUnderscore())->text(dm::getI18n()->__($module->getPlural())).
      £('p.infos',
        ($module->hasModel()
        ? format_number_choice('[0]no element|[1]1 element|(1,+Inf]%1% elements', array('%1%' => $nbObjects), $nbObjects)
        : '')
      )
    );
  }

  echo £c('ul');

echo £c('div');