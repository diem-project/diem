<?php use_helper('Form');

echo £o('div#dm_tool_bar.clearfix');

  echo £link('dmAuth/signout')->nameTitle(__('Logout'))->set('.widget16.s16block.s16_signout');

  echo £link()->nameTitle(dm::getI18n()->__('Home'))->set('.widget16.s16block.s16_home');

  echo £link('dmService/launch?name=dmRefresh')->nameTitle(__('Clear Cache'))->set('.widget16.s16block.s16_clear');

//  echo £link('dmCodeEditor/index')->nameTitle(__('Code Editor'))->set('.widget16.s16block.s16_code_editor');

  echo £("div.dm_menu.widget16",
    $menu->render(array(
      'level0_ul_class' => 'ui-helper-reset',
      'level0_li_class' => 'ui-corner-top ui-state-default',
      'level1_ul_class' => 'ui-widget ui-widget-content'
    ))
  );

  echo £('div.widget16.mt5',
    select_tag(
      'dm_select_culture',
      options_for_select($cultures, $sf_user->getCulture())
    )
  );

  if (sfConfig::get('dm_html_validate', true) && $sf_user->can('html_validate_admin'))
  {
    echo '<div id="dm_html_validate" class="widget16">validation...</div>';
  }

  if ($sf_request->useTidy() && $sf_user->can('tidy_output'))
  {
  	echo '__DM_TIDY_OUTPUT__';
  }

  echo £link('app:front')->nameTitle(__('Go to site'))->set('.widget16');
  
  if (sfConfig::get('sf_web_debug'))
  {
    echo '__SF_WEB_DEBUG__';
  }

echo £c('div');