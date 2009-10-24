<?php

echo £o('ul.dm_module_spaces.dm_module_type.mt10');

  foreach($spaces as $space)
  {
    echo £o('li.dm_module_space.dm_module_type_show.dm_box.fleft.mr20.mb20');
    
    echo £('h2.title', £link($sf_context->getRouting()->getModuleSpaceUrl($space))->text(__($space->getPublicName()))->set('.center'));
    
    echo £o('ul.dm_modules.dm_box_inner.pl10.pr10');
    
	  foreach(dmArray::get($menu->getSpaceMenu($space), 'menu', array()) as $key => $moduleArray)
	  {
	    $module = $moduleManager->getModule($key);
	    
      $nbRecords = $module->hasModel() ? $module->getTable()->count() : null;
      
	  	echo £('li.dm_module',
	      £link($moduleArray['link'])->text($moduleArray['name']).
	      £('p.infos', $nbRecords ? format_number_choice('[0]no element|[1]1 element|(1,+Inf]%1% elements', array('%1%' => $nbRecords), $nbRecords) : '')
	  	);
	  }
	  echo £c('ul').£c('li');
  }

echo £c('ul');