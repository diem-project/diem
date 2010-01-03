<?php

echo £o('div.dm_code_editor_wrap');

echo £o('div.dm_code_editor');

echo £o('ul.tabs');

  echo £('li.dm_file_open',
    £('a.s16block.s16_folder_open href=#dm_code_editor_file_open title=Open', 'Open')
  );
  
echo £c('ul');

echo '<div id="dm_code_editor_file_open">'.$fileMenu->render().'</div>';

echo £c('div');

echo £c('div');