<?php

echo _open('div#dm_tool_bar.clearfix.'.sfConfig::get('dm_toolBar_flavour', 'blue'));

  if ($sf_user->can('clear_cache'))
  {
    echo _link('dmCore/refresh')->text('')->title(__('Update project'))->set('.tipable.dm_refresh_link.widget16.s16block.s16_clear');
  }

  if($sf_user->can('code_editor'))
  {
    echo _link('dmCodeEditor/index')->text('')->title(__('Code Editor'))->set('.tipable.widget16.s16block.s16_code_editor');
  }

  echo _tag('div.dm_menu.widget16', $menu->render());

  if (isset($cultureSelect))
  {
    echo _tag('div.widget16.mt3', $cultureSelect->render('dm_select_culture', $sf_user->getCulture()));
  }

  if (dmAPCCache::isEnabled() && $sf_user->can('systeme'))
  {
    $apcLoad = dmAPCCache::getLoad();
    echo _link('dmServer/apc')
    ->set('.tipable.dm_load_monitor.fleft')
    ->title(sprintf('APC usage: %s / %s', $apcLoad['usage'], $apcLoad['limit']))
    ->text(sprintf('<span style="height: %dpx;"></span>', round($apcLoad['percent'] * 0.21)));
  }

  echo _link('app:front')->text(__('Go to site'))->set('.widget16.ml10');

  if(sfConfig::get('dm_locks_enabled'))
  {
    echo _tag('div.dm_active_users', '');
  }

  if($dmUser = $sf_user->getDmUser())
  {
    echo _link('@signout')
    ->text('')
    ->title(__('Logout'))
    ->set('.tipable.widget16.fright.s16block.s16_signout');

    echo _link('dmUserAdmin/myAccount')
    ->text($dmUser->get('username'))
    ->title(__('My account'))
    ->set('.tipable.widget16.fright');
  }

  if (sfConfig::get('sf_web_debug'))
  {
    echo '__SF_WEB_DEBUG__';
  }

echo _close('div');