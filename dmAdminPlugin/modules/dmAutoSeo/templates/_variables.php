<?php

echo _open('div.dm_variables');

echo _tag('div.dm_info.ui-corner-all',
  _tag('span.s16block.s16_help.fleft.mr5').' '.__('Variables you can use here:')
);

echo _open('ul.dm_modules.dm_accordion.mt10');

foreach($modules as $module)
{
  echo _open('li.dm_module');
  
  echo _tag('h3.dm_module_name', _tag('a href=#', __($module->getName())));
  
  echo _open('ul.dm_variables');
  
  foreach($module->getTable()->getSeoColumns() as $variable)
  {
    echo _tag('li.dm_variable', $seoSynchronizer->wrap($module->getUnderscore().'.'.$variable));
  }
  
  echo _close('ul'), _close('li');
}

echo _close('ul'), _close('div');