<?php

echo £o('div.dm_box.big.console');

echo £('h1.title', __('Console'));

echo £o('div.dm_box_inner');

echo £o('div#dm_console');
  echo £o('ul#dm_lines');
    echo £("li.dm_command_intro", sprintf("Logged as %s on %s", $whoami, $uname));
    echo £("li.dm_command_intro", str_repeat("-", 20));
    echo £("li.dm_command_intro", "Current working directory : ".$pwd);
    echo £("li.dm_command_intro", "Commands Available :");
    echo £("li.dm_command_intro", "<strong>".$commands."</strong>");
    echo £("li.dm_command_intro", "Symfony commands can be run by prefixing with sf<br />Exemple : sf cc ( clear cache )");
    echo £("li.dm_command_intro", str_repeat("-", 20));
  echo £c('ul');
  echo £o('ul#dm_lines.dm_content_command');
    echo £('li','&nbsp;');
  echo £c('ul');
  echo £o("div#dm_command_wrap.clearfix");
    echo $form->open("action=dmConsole/command");
      echo $form['dm_command']->renderLabel($prompt, array('class' => 'dm_prompt_command')), $form['dm_command'];
    echo £c('form');
  echo £c('div');
echo £c('div');

echo £c('div'), £c('div');