<?php

echo £o('div.dm_variables');

echo £('div.dm_info.ui-corner-all',
  £('span.s16block.s16_help.fleft.mr5').' Open a tab to see the variables you can use here.'
);

echo £o('ul.dm_modules.dm_accordion.mt10');

foreach($modules as $module)
{
  echo £o('li.dm_module');
  
  echo £('h3.dm_module_name', £('a href=#', $module->getName()));
  
  echo £o('ul.dm_variables');
  
  foreach($module->getTable()->getSeoColumns() as $variable)
  {
    echo £('li.dm_variable', $seoSynchronizer->wrap($module->getKey().'.'.$variable));
  }
  
  echo £c('ul'), £c('li');
}

echo £c('ul'), £c('div');