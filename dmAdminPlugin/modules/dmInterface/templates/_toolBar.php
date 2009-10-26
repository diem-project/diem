<?php

echo £o('div#dm_tool_bar.clearfix.'.sfConfig::get('dm_toolBar_flavour', 'blue'));

  echo £link('dmAuth/signout')->textTitle($sf_user->getUsername().' : '.__('Logout'))->set('.widget16.s16block.s16_signout');

//  echo £link()->textTitle(__('Home'))->set('.widget16.s16block.s16_home');

  if ($sf_user->can('clear_cache'))
  {
    echo £link('dmCore/refresh')->textTitle(__('Update project'))->set('.dm_refresh_link.widget16.s16block.s16_clear');
  }
  
//  echo £link('dmCodeEditor/index')->textTitle(__('Code Editor'))->set('.widget16.s16block.s16_code_editor');

  echo £o("div.dm_menu.widget16"),
    $menu->render(array(
      'level0_ul_class' => 'ui-helper-reset',
      'level0_li_class' => 'ui-corner-top ui-state-default',
      'level1_ul_class' => 'ui-widget ui-widget-content'
    )),
  £c('div');

  if (isset($cultureSelect))
  {
    echo £('div.widget16.mt3', $cultureSelect->render('dm_select_culture', $sf_user->getCulture()));
  }
  
  if (dmAPCCache::isEnabled() && $sf_user->can('systeme'))
  {
    $apcLoad = dmAPCCache::getLoad();
    echo £link('dmServer/apc')
    ->set('.dm_load_monitor.fleft')
    ->title(sprintf('APC load : %s / %s', $apcLoad['usage'], $apcLoad['limit']))
    ->text(sprintf('<span style="height: %dpx;"></span>', round($apcLoad['percent'] * 0.21))); 
  }

  if (sfConfig::get('dm_html_validate', true) && $sf_user->can('html_validate_admin'))
  {
    echo '<div id="dm_html_validate" class="widget16">validation...</div>';
  }

  if ($sf_request->useTidy() && $sf_user->can('tidy_output'))
  {
    echo '__DM_TIDY_OUTPUT__';
  }

  echo £link('app:front')->textTitle(__('Go to site'))->set('.widget16.ml10');
  
  if (sfConfig::get('sf_web_debug'))
  {
    echo '__SF_WEB_DEBUG__';
  }

echo £c('div');