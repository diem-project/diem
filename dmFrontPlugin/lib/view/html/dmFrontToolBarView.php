<?php

class dmFrontToolBarView extends dmToolBarView
{
  public function render()
  {
    return
    $this->helper->open('div#dm_tool_bar.dm.clearfix.'.sfConfig::get('dm_toolBar_flavour', 'blue')).
    $this->renderClearCache().
    $this->renderCodeEditor().
    $this->renderCultureSelect().
    $this->renderThemeSelect().
    $this->renderPageAdd().
    $this->renderPageEdit().
    $this->renderShowPageStructure().
    $this->renderWidgetAdd().
    $this->renderGoToAdmin().
    $this->renderUserLinks().
    $this->renderSfWebDebug().
    $this->helper->close('div');
  }

  protected function renderClearCache()
  {
    if ($this->user->can('clear_cache'))
    {
      return $this->helper->link('+/dmCore/refresh')
      ->text('')
      ->title($this->i18n->__('Update project'))
      ->set('.tipable.dm_refresh_link.widget16.s16block.s16_clear');
    }
  }

  protected function renderCodeEditor()
  {
    if($this->user->can('code_editor'))
    {
      return $this->helper->link('+/dmCodeEditor/launch')
      ->text('')
      ->title($this->i18n->__('Code Editor'))
      ->set('.tipable.code_editor.widget16.s16block.s16_code_editor');
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

  protected function renderThemeSelect()
  {
    if ($this->container->getService('theme_manager')->getNbThemesEnabled() > 1)
    {
      $themeSelect = new sfWidgetFormSelect(array('choices' => $this->container->getService('theme_manager')->getThemesEnabled()));
    
      return $this->helper->tag('div.widget16.mt3',
        $themeSelect->render('dm_select_theme', $this->user->getTheme()->getName())
      );
    }
  }

  protected function renderPageAdd()
  {
    if($this->user->can('page_add'))
    {
      return $this->helper->link('+/dmPage/new')
      ->set('.tipable.page_add_form.widget24.s24block.s24_page_add')
      ->text('')
      ->title($this->i18n->__('Add new page'));
    }
  }

  protected function renderPageEdit()
  {
    if($this->user->can('page_edit'))
    {
      return $this->helper->link('+/dmPage/edit')
      ->set('.tipable.page_edit_form.widget24.s24block.s24_page_edit')
      ->text('')
      ->title($this->i18n->__('Edit page'));
    }
  }

  protected function renderShowPageStructure()
  {
    if ($this->user->can('zone_add, widget_add'))
    {
      return $this->helper->tag('a.tipable.edit_toggle.widget24.s24block.s24_view_'.($this->user->getIsEditMode() ? 'on' : 'off'), array('title' => $this->i18n->__('Show page structure')), '');
    }
  }

  protected function renderWidgetAdd()
  {
    if($this->user->can('widget_add'))
    {
      return $this->helper->tag('div.dm_menu.dm_add_menu', array('json' =>array(
        'reload_url' => $this->helper->link('+/dmInterface/reloadAddMenu')->getHref()
      )), $this->helper->tag('a.widget24.s24block.s24_add.dm_fake_link'));
    }
  }

  protected function renderGoToAdmin()
  {
    return $this->helper->link('app:admin')
    ->text(__('Go to admin'))
    ->set('.widget16.s16.s16_arrow_return');
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

      $html .= $this->helper->link('app:admin/+/dmUserAdmin/myAccount')
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