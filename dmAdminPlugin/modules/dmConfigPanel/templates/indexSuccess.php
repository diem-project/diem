<?php
use_stylesheet('lib.ui-tabs');
use_stylesheet('admin.configPanel');
use_javascript('lib.ui-tabs');
use_javascript('core.tabForm');
use_javascript('admin.configPanel');

echo _open('div.dm_config_panel.mt10');

echo _open('ul');
foreach($groups as $group)
{
  echo _tag('li', sprintf('<a href="#%s">%s</a>', 'dm_setting_group_'.dmString::slugify($group), __(dmString::humanize($group))));
}
echo _close('ul');

echo $form->open('.dm_form.list');

foreach($settings as $group => $groupSettings)
{
  if ('internal' == $group)
  {
    continue;
  }
  echo _open('div#dm_setting_group_'.dmString::slugify($group));
  
  echo _tag('h2', __(dmString::humanize($group)));
  
  echo _open('ul.dm_setting_group.clearfix');
  $it = 0;
  foreach($groupSettings as $setting)
  {
    $settingName = $setting->get('name');
    
    if (!($it%2))
    {
      echo _close('ul')._open('ul.dm_setting_group.clearfix');
    }
    ++$it;
    
    echo _tag('li.dm_form_element.clearfix.setting_'.$setting->type,
      $form[$settingName]->label()->field()->error().
      _tag('div.dm_help_wrap', escape(__($form[$settingName]->getHelp())))
    );
  }
  echo _close('ul');
  
  echo _close('div');
}

echo $form->renderSubmitTag(__('Save modifications'));

echo '</form>';

echo _close('div');