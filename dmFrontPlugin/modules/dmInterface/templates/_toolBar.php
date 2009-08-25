<?php use_helper('Form');

echo £o('div#dm_tool_bar.dm.clearfix');

  echo £('a.show_tool_bar_toggle.s16block.s16_chevron_'.($sf_user->getShowToolBar() ? 'down' : 'up'), '+');

  echo £link('+/dmAuth/signout')->nameTitle(__('Logout'))->set('.widget16.s16block.s16_signout');
  
  echo £link('app:admin')->nameTitle(__('Administration'))->set('.widget16.s16block.s16_home');

  echo £link('+/dmService/launch?name=dmRefresh')->nameTitle(__('Clear Cache'))->set('.widget16.s16block.s16_clear');
  
  if($sf_user->can('code_editor'))
  {
  	echo £link('+/dmCodeEditor/launch')->nameTitle(__('Code Editor'))->set('.code_editor.widget16.s16block.s16_code_editor');
  }

  echo £('div.widget16.mt5',
    select_tag('dm_select_culture', options_for_select($cultures, $sf_user->getCulture())
    )
  );

  echo £('div.widget16.mt5',
    select_tag('dm_select_theme', options_for_select($themes, $sf_user->getThemeKey())
    )
  );

  if (sfConfig::get('dm_html_validate', true) && $sf_user->can('html_validate_front'))
  {
    printf('<div id="dm_html_validate" class="widget16">%s ...</div>', __('Validation'));
  }

  if ($sf_request->useTidy() && $sf_user->can('tidy_output'))
  {
  	echo '__DM_TIDY_OUTPUT__';
  }
  
  echo £link('+/dmPage/edit')
  ->set('a.page_edit_form.widget24.s24block.s24_page_edit')
  ->nameTitle(__('Edit page'));
  
  echo £link('+/dmPage/new')
  ->set('a.page_add_form.widget24.s24block.s24_page_add')
  ->nameTitle(__('Add new page'));

  if ($sf_user->can('zone_add widget_add'))
  {
    echo £('a.edit_toggle.widget24.s24block.s24_view_'.($sf_user->getIsEditMode() ? 'on' : 'off'), array('title' => __('Show page structure')),
      __('Add')
    );
  }

  if(isset($addMenu))
  {
    echo £('div.dm_menu.widget16.dm_add_menu', $addMenu->render(array(
      'level0_ul_class' => 'ui-helper-reset',
      'level0_li_class' => 'ui-corner-bottom ui-state-default',
      'level1_ul_class' => 'ui-widget ui-widget-content',
      'level2_ul_class' => 'clearfix'
    )));
  }
  
  if (sfConfig::get('sf_web_debug'))
  {
  	echo '__SF_WEB_DEBUG__';
  }

echo £c('div');