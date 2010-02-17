<?php 

echo
_tag('div#content_message_editor.clearfix.mt10', ''),

_tag('div#dm_code_editor.clearfix', array('json' => array(
  'get_dir_content_url' => _link('+/dmCodeEditor/getDirContent')->getHref(),
  'save_file_url' => _link('+/dmCodeEditor/saveFile')->getHref(),
  'open_file_url' => _link('+/dmCodeEditor/openFile')->getHref(),
  'path_replacements' => array_flip($editor->getOption('path_replacements'))
)),
  
  _tag('div#dm_code_editor_tree.ui-corner-all',
    _tag('p.dm_root_dir.ui-corner-top', _tag('span.s16.s16_drive_arrow.block', '&nbsp;'.dmProject::getRootDir())).
    _tag('div#dm_code_editor_tree_inner')
  ).
  
  _tag('div#dm_code_editor_content',
    _tag('div.dm_code_editor_content_tabs',
      _tag('ul')
    )
  )
);