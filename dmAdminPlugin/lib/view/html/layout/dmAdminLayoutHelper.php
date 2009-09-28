<?php

class dmAdminLayoutHelper extends dmCoreLayoutHelper
{

  public function renderMetas()
  {
    return sprintf('<title>%s</title>', $this->response->getTitle());
  }
  
  public function renderBodyTag($class = null)
  {
    $actionName = $this->actionStack->getLastEntry()->getActionName();
    
    return sprintf('<body class="dm%s%s%s%s">',
      $actionName == 'index' ? ' list' : '',
      in_array($actionName, array('edit', 'new', 'update', 'create')) ? ' form' : '',
      sfConfig::get('dm_admin_full_screen') ? ' full_screen' : '',
      $class ? ' '.$class : ''
    );
  }
  
  public function renderEditBars()
  {
    if (!$this->user->can('admin'))
    {
      return '';
    }
    
    $cacheKey = sfConfig::get('sf_cache') ? $this->user->getCredentialsHash() : null;
    
    $html = '';
    
    if (sfConfig::get('dm_pageBar_enabled', true) && $this->user->can('page_bar_admin'))
    {
      $html .= $this->helper->renderPartial('dmInterface', 'pageBar', array('cacheKey' => $cacheKey));
    }
    
    if (sfConfig::get('dm_mediaBar_enabled', true) && $this->user->can('media_bar_admin'))
    {
      $html .= $this->helper->renderPartial('dmInterface', 'mediaBar', array('cacheKey' => $cacheKey));
    }
    
    if ($this->user->can('tool_bar_admin'))
    {
      $html .= $this->helper->renderComponent('dmInterface', 'toolBar', array('cacheKey' => $cacheKey));
    }
    
    return $html;
  }


}