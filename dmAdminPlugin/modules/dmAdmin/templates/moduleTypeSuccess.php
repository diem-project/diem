<?php

echo £o('div.dm_module_type.dm_module_type_show.dm_box.little');

  echo £('h1.title', __($type->getPublicName()));

  echo £o('ul.dm_modules.dm_box_inner');

  foreach($spaces as $space)
  {
  	echo £o('li.dm_module_space');
  	echo £('h2.title2', dmAdminHelper::getLinkToModuleSpace($space));
  	echo £o('ul.dm_modules');
	  foreach($space->getModules() as $module)
	  {
      $nbObjects = $module->hasModel() ? $module->getTable()->count() : null;
	  	echo £('li.dm_module',
	      £link('@'.$module->getUnderscore())->name(dm::getI18n()->__($module->getPlural())).
	      £('p.infos',
	        ($module->hasModel()
	        ? format_number_choice('[0]no element|[1]1 element|(1,+Inf]%1% elements', array('%1%' => $nbObjects), $nbObjects)
	        : '')
	      )
	  	);
	  }
	  echo £c('ul');
	  echo £c('li');
  }

  echo £c('ul');

echo £c('div');