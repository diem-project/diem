<?php 

echo

£('div.clearfix',
  £('h1.fleft.mr30', 'Code editor').
  £('div#content_message_editor.clearfix', '')
),

£('div#dm_code_editor.clearfix',
  
  £('div#dm_code_editor_tree.dm_tree').
  
  £('div#dm_code_editor_content',
    £('div.code_editor_content_tabs',
      £('ul')
    )
  )
);