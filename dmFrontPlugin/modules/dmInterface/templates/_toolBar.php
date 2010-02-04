<?php

echo _open('div#dm_tool_bar.dm.clearfix.'.sfConfig::get('dm_toolBar_flavour', 'blue'));

  echo _tag('a.show_tool_bar_toggle.s16block.s16_chevron_'.($sf_user->getShowToolBar() ? 'down' : 'up'), '+');

  echo _link('+/dmAuth/signout')->text('')->title($sf_user->getUsername().' : '.__('Logout'))->set('.widget16.s16block.s16_signout');
  
  echo _link('app:admin')->text('')->title(__('Administration'))->set('.widget16.s16block.s16_home');
  
  if ($sf_user->can('clear_cache'))
  {
    echo _link('+/dmCore/refresh')->text('')->title(__('Clear cache'))->set('.dm_refresh_link.widget16.s16block.s16_clear');
  }
  
  if($sf_user->can('code_editor'))
  {
    echo _link('+/dmCodeEditor/launch')->text('')->title(__('Code Editor'))->set('.code_editor.widget16.s16block.s16_code_editor');
  }

  if (isset($cultureSelect))
  {
    echo _tag('div.widget16.mt3', $cultureSelect->render('dm_select_culture', $sf_user->getCulture()));
  }
  
  if (isset($themeSelect))
  {
    echo _tag('div.widget16.mt3', $themeSelect->render('dm_select_theme', $sf_user->getTheme()->getKey()));
  }
  
  if ($sf_user->can('page_edit'))
  {
    echo _link('+/dmPage/edit')->set('a.page_edit_form.widget24.s24block.s24_page_edit')->text('')->title(__('Edit page'));
  }
  
  if ($sf_user->can('page_add'))
  {
    echo _link('+/dmPage/new')->set('a.page_add_form.widget24.s24block.s24_page_add')->text('')->title(__('Add new page'));
  }

  if ($sf_user->can('zone_add, widget_add'))
  {
    echo _tag('a.edit_toggle.widget24.s24block.s24_view_'.($sf_user->getIsEditMode() ? 'on' : 'off'), array('title' => __('Show page structure')), '');
  }

  if($sf_user->can('widget_add'))
  {
    echo _tag('div.dm_menu.widget16.dm_add_menu', array('json' =>array(
      'reload_url' => _link('+/dmInterface/reloadAddMenu')->getHref()
    )), '...');
  }
  
  if (sfConfig::get('sf_web_debug'))
  {
    echo '__SF_WEB_DEBUG__';
  }

echo _close('div');