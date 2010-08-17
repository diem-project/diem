<?php

class dmAdminToolBarView extends dmToolBarView
{
  public function render()
  {
    return
    $this->helper->open('div#dm_tool_bar.clearfix.'.sfConfig::get('dm_toolBar_flavour', 'blue')).
    $this->renderClearCache().
    $this->renderCodeEditor().
    $this->renderMenu().
    $this->renderCultureSelect().
    $this->renderApcLoad().
    $this->renderGoToSite().
    $this->renderActiveUsers().
    $this->renderUserLinks().
    $this->renderSfWebDebug().
    $this->helper->close('div');
  }

  protected function renderClearCache()
  {
    if ($this->user->can('clear_cache'))
    {
      return $this->helper->link('dmCore/refresh')
      ->text('')
      ->title($this->i18n->__('Update project'))
      ->set('.tipable.dm_refresh_link.widget16.s16block.s16_clear');
    }
  }

  protected function renderCodeEditor()
  {
    if($this->user->can('code_editor'))
    {
      return $this->helper->link('dmCodeEditor/index')
      ->text('')
      ->title($this->i18n->__('Code Editor'))
      ->set('.tipable.widget16.s16block.s16_code_editor');
    }
  }

  protected function renderMenu()
  {
    return $this->helper->tag('div.dm_menu.widget16',
      $this->container->getService('admin_menu')->build()->render()
    );
  }

  protected function renderCultureSelect()
  {
    if ($cultureSelect = $this->getCultureSelect())
    {
      return $this->helper->tag('div.widget16.mt3',
        $cultureSelect->render('dm_select_culture', $this->user->getCulture())
      );
    }
  }

  protected function renderApcLoad()
  {
    if (dmAPCCache::isEnabled() && $this->user->can('systeme'))
    {
      $apcLoad = dmAPCCache::getLoad();
      
      return $this->helper->link('dmServer/apc')
      ->set('.tipable.dm_load_monitor.fleft')
      ->title(sprintf('APC usage: %s / %s', $apcLoad['usage'], $apcLoad['limit']))
      ->text(sprintf('<span style="height: %dpx;"></span>', round($apcLoad['percent'] * 0.21)));
    }
  }

  protected function renderGoToSite()
  {
    return $this->helper->link('app:front')
    ->text(__('Go to site'))
    ->set('.widget16.ml10.s16.s16_arrow_return_180');
  }

  protected function renderActiveUsers()
  {
    if(sfConfig::get('dm_locks_enabled'))
    {
      return $this->helper->tag('div.dm_active_users', '');
    }
  }

  protected function renderUserLinks()
  {
    $html = '';
    
    if($dmUser = $this->user->getDmUser())
    {
      $html .= $this->helper->link('@signout')
      ->text('')
      ->title(__('Logout'))
      ->set('.tipable.widget16.fright.s16block.s16_signout');

      $html .= $this->helper->link('dmUserAdmin/myAccount')
      ->text($dmUser->get('username'))
      ->title(__('My account'))
      ->set('.tipable.widget16.fright');
    }

    return $html;
  }

  protected function renderSfWebDebug()
  {
    if (sfConfig::get('sf_web_debug'))
    {
      return '__SF_WEB_DEBUG__';
    }
  }
}