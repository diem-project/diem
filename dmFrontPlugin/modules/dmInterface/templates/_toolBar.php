<?php

echo _open('div#dm_tool_bar.dm.clearfix.'.sfConfig::get('dm_toolBar_flavour', 'blue'));

  if ($sf_user->can('clear_cache'))
  {
    echo _link('+/dmCore/refresh')->text('')->title(__('Clear cache'))->set('.tipable.dm_refresh_link.widget16.s16block.s16_clear');
  }
  
  if($sf_user->can('code_editor'))
  {
    echo _link('+/dmCodeEditor/launch')->text('')->title(__('Code Editor'))->set('.tipable.code_editor.widget16.s16block.s16_code_editor');
  }

  if (isset($cultureSelect))
  {
    echo _tag('div.widget16.mt3', $cultureSelect->render('dm_select_culture', $sf_user->getCulture()));
  }
  
  if (isset($themeSelect))
  {
    echo _tag('div.widget16.mt3', $themeSelect->render('dm_select_theme', $sf_user->getTheme()->getName()));
  }
  
  if ($sf_user->can('page_add'))
  {
    echo _link('+/dmPage/new')->set('a.tipable.page_add_form.widget24.s24block.s24_page_add')->text('')->title(__('Add new page'));
  }

  if ($sf_user->can('page_edit'))
  {
    echo _link('+/dmPage/edit')->set('.tipable.page_edit_form.widget24.s24block.s24_page_edit')->text('')->title(__('Edit page'));
  }

  if ($sf_user->can('zone_add, widget_add'))
  {
    echo _tag('a.tipable.edit_toggle.widget24.s24block.s24_view_'.($sf_user->getIsEditMode() ? 'on' : 'off'), array('title' => __('Show page structure')), '');
  }

  if($sf_user->can('widget_add'))
  {
    echo _tag('div.dm_menu.dm_add_menu', array('json' =>array(
      'reload_url' => _link('+/dmInterface/reloadAddMenu')->getHref()
    )), £('a.widget24.s24block.s24_add.dm_fake_link'));
  }

  echo _link('app:admin')->text(__('Go to admin'))->set('.widget16');
  
  if($dmUser = $sf_user->getDmUser())
  {
    echo _link('@signout')
    ->text('')
    ->title(__('Logout'))
    ->set('.tipable.widget16.fright.s16block.s16_signout');

    echo _link('app:admin/+/dmUserAdmin/myAccount')
    ->text($dmUser->get('username'))
    ->title(__('My account'))
    ->set('.tipable.widget16.fright');
  }

  if (sfConfig::get('sf_web_debug'))
  {
    echo '__SF_WEB_DEBUG__';
  }

echo _close('div');