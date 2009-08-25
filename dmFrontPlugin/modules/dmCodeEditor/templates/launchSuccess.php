<?php

echo £o('div.dm_code_editor_wrap');

echo £o('div.dm_code_editor');

echo £o('ul.tabs');

  echo £('li.dm_file_open',
    £('a.s16block.s16_folder_open href=#dm_code_editor_file_open title=Open', 'Open')
  );
  
echo £c('ul');

echo '<div id="dm_code_editor_file_open">'.$fileMenu->render('level0_ul_class=clearfix level0_li_class=type').'</div>';

echo £c('div');

echo £c('div');

if ($css)
{
  echo sprintf('<style type="text/css">%s</style>', $css);
}

if ($js)
{
  echo '__DM_SPLIT__', $js;
}