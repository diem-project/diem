<?php

echo £o('div#dm_tool_bar.dm.clearfix');

  echo £('a.show_tool_bar_toggle.s16block.s16_chevron_'.($sf_user->getShowToolBar() ? 'down' : 'up'), '+');

  echo £link('+/dmAuth/signout')->textTitle($sf_user->getUsername().' : '.__('Logout'))->set('.widget16.s16block.s16_signout');
  
  echo £link('app:admin')->textTitle(__('Administration'))->set('.widget16.s16block.s16_home');

  echo £link('+/dmService/launch?name=dmRefresh')->textTitle(__('Clear Cache'))->set('.widget16.s16block.s16_clear');
  
  if($sf_user->can('code_editor'))
  {
    echo £link('+/dmCodeEditor/launch')->textTitle(__('Code Editor'))->set('.code_editor.widget16.s16block.s16_code_editor');
  }

  if (isset($cultureSelect))
  {
    echo £('div.widget16.mt5', $cultureSelect->render('dm_select_culture', $sf_user->getCulture()));
  }
  
  echo £('div.widget16.mt5', $themeSelect->render('dm_select_theme', $sf_user->getTheme()->getKey()));

  if (sfConfig::get('dm_html_validate', true) && $sf_user->can('html_validate_front'))
  {
    echo '<div id="dm_html_validate" class="widget16">'.__('Validation').' ...</div>';
  }

  if ($sf_request->useTidy() && $sf_user->can('tidy_output'))
  {
    echo '__DM_TIDY_OUTPUT__';
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