<?php

echo _tag('div.dm_code_editor_wrap',
  _tag('div.dm_code_editor',
    _tag('ul.tabs',
      _tag('li.dm_file_open',
        _tag('a.s16block.s16_folder_open href=#dm_code_editor_file_open title=Open', 'Open')
      )
    ).
    _tag('div#dm_code_editor_file_open', $fileMenu->render())
  )
);