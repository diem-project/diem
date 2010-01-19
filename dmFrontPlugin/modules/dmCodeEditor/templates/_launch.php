<?php

echo £('div.dm_code_editor_wrap',
  £('div.dm_code_editor',
    £('ul.tabs',
      £('li.dm_file_open',
        £('a.s16block.s16_folder_open href=#dm_code_editor_file_open title=Open', 'Open')
      )
    ).
    £('div#dm_code_editor_file_open', $fileMenu->render())
  )
);