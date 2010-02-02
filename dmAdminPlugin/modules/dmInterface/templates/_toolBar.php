<?php

echo _open('div#dm_tool_bar.clearfix.'.sfConfig::get('dm_toolBar_flavour', 'blue'));

  echo _link('dmAuth/signout')->textTitle($sf_user->getUsername().' : '.__('Logout'))->set('.widget16.s16block.s16_signout');

  if ($sf_user->can('clear_cache'))
  {
    echo _link('dmCore/refresh')->textTitle(__('Update project'))->set('.dm_refresh_link.widget16.s16block.s16_clear');
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
    ->set('.dm_load_monitor.fleft')
    ->title(sprintf('APC load : %s / %s', $apcLoad['usage'], $apcLoad['limit']))
    ->text(sprintf('<span style="height: %dpx;"></span>', round($apcLoad['percent'] * 0.21)));
  }

  echo _link('app:front')->textTitle(__('Go to site'))->set('.widget16.ml10');

  if(sfConfig::get('dm_locks_enabled'))
  {
    echo _tag('div.dm_active_users', '');
  }

  if (sfConfig::get('sf_web_debug'))
  {
    echo '__SF_WEB_DEBUG__';
  }

echo _close('div');