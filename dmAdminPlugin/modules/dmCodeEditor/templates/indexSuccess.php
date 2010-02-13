<?php 

echo

_tag('div.clearfix',
  _tag('h1.fleft.mr30', 'Code editor').
  _tag('div#content_message_editor.clearfix', '')
),

_tag('div#dm_code_editor.clearfix',
  
  _tag('div#dm_code_editor_tree.dm_tree').
  
  _tag('div#dm_code_editor_content',
    _tag('div.code_editor_content_tabs',
      _tag('ul')
    )
  )
);