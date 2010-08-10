<?php

echo _open('div#dm_console.mt10');
  echo _open('ul#dm_lines');
    echo _tag("li.dm_command_intro", sprintf(__("Logged as %s on %s"), $whoami, $uname));
    echo _tag("li.dm_command_intro", str_repeat("-", 20));
    echo _tag("li.dm_command_intro", __("Current working directory: ").$pwd);
    echo _tag("li.dm_command_intro", __("Commands Available:"));
    echo _tag("li.dm_command_intro", "<strong>".$commands."</strong>");
    echo _tag("li.dm_command_intro", __("symfony commands can be run by prefixing with sf")._tag('br').__("Example : sf cc (clear cache)"));
    echo _tag("li.dm_command_intro", str_repeat("-", 20));
  echo _close('ul');
  echo _open('ul#dm_lines.dm_content_command');
    echo _tag('li','&nbsp;');
  echo _close('ul');
  echo _open("div#dm_command_wrap.clearfix");
    echo $form->open("action=dmConsole/command");
      echo $form['dm_command']->renderLabel($prompt, array('class' => 'dm_prompt_command')), $form['dm_command'];
    echo _close('form');
  echo _close('div');
echo _close('div');

