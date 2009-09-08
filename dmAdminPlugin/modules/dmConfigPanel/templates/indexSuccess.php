<?php
use_stylesheet('lib.ui-tabs');
use_stylesheet('admin.configPanel');
use_javascript('lib.ui-tabs');
use_javascript('admin.configPanel');

echo £o('div.dm_box.big.sitemap');

echo £('h1.title', __('Edit configuration'));

echo £o('div.dm_box_inner.dm_config_panel');

echo £o('ul');
foreach($groups as $group)
{
  echo £('li', sprintf('<a href="#%s">%s</a>', dmString::slugify($group), __(dmString::humanize($group))));
}
echo £c('ul');

echo $form->open('.dm_form.list');

foreach($settings as $group => $groupSettings)
{
  echo £o('div#'.dmString::slugify($group));
  
  echo £('h2', __(dmString::humanize($group)));
  
  echo £o('ul.dm_setting_group.clearfix');
  foreach($groupSettings as $setting)
  {
    echo $form[$setting->get('name')]->renderRow();
  }
  echo £c('ul');
  
  echo £c('div');
}

echo $form->renderSubmitTag();

echo '</form>';

echo £c('div');

echo £c('div');