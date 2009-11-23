<?php

echo £o('div#dm_tool_bar.dm.clearfix.'.sfConfig::get('dm_toolBar_flavour', 'blue'));

  echo £('a.show_tool_bar_toggle.s16block.s16_chevron_'.($sf_user->getShowToolBar() ? 'down' : 'up'), '+');

  echo £link('+/dmAuth/signout')->textTitle($sf_user->getUsername().' : '.__('Logout'))->set('.widget16.s16block.s16_signout');
  
  echo £link('app:admin')->textTitle(__('Administration'))->set('.widget16.s16block.s16_home');
  
  if ($sf_user->can('clear_cache'))
  {
    echo £link('+/dmCore/refresh')->textTitle(__('Clear cache'))->set('.dm_refresh_link.widget16.s16block.s16_clear');
  }
  
  if($sf_user->can('code_editor'))
  {
    echo £link('+/dmCodeEditor/launch')->textTitle(__('Code Editor'))->set('.code_editor.widget16.s16block.s16_code_editor');
  }

  if (isset($cultureSelect))
  {
    echo £('div.widget16.mt3', $cultureSelect->render('dm_select_culture', $sf_user->getCulture()));
  }
  
  if (isset($themeSelect))
  {
    echo £('div.widget16.mt3', $themeSelect->render('dm_select_theme', $sf_user->getTheme()->getKey()));
  }
  
  if ($sf_user->can('page_edit'))
  {
    echo £link('+/dmPage/edit')->set('a.page_edit_form.widget24.s24block.s24_page_edit')->textTitle(__('Edit page'));
  }
  
  if ($sf_user->can('page_add'))
  {
    echo £link('+/dmPage/new')->set('a.page_add_form.widget24.s24block.s24_page_add')->textTitle(__('Add new page'));
  }

  if ($sf_user->can('zone_add widget_add'))
  {
    echo £('a.edit_toggle.widget24.s24block.s24_view_'.($sf_user->getIsEditMode() ? 'on' : 'off'), array('title' => __('Show page structure')),
      __('Add')
    );
  }

  if(isset($addMenu))
  {
    echo £o('div.dm_menu.widget16.dm_add_menu'),
    $addMenu->render(array(
      'level0_ul_class' => 'ui-helper-reset',
      'level0_li_class' => 'ui-corner-bottom ui-state-default',
      'level1_ul_class' => 'ui-widget ui-widget-content',
      'level2_ul_class' => 'clearfix'
    )),
    £c('div');
  }
  
  if (sfConfig::get('sf_web_debug'))
  {
    echo '__SF_WEB_DEBUG__';
  }

echo £c('div');