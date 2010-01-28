<?php

class dmAdminLayoutHelper extends dmCoreLayoutHelper
{
  
  public function renderBodyTag($class = null)
  {
    $actionName = $this->serviceContainer->getParameter('controller.action');
    
    return sprintf('<body class="dm%s%s%s%s">',
      $actionName == 'index' ? ' list' : '',
      in_array($actionName, array('edit', 'new', 'update', 'create')) ? ' form' : '',
      sfConfig::get('dm_admin_full_screen') ? ' full_screen' : '',
      $class ? ' '.$class : ''
    );
  }
  
  public function renderEditBars()
  {
    $user = $this->serviceContainer->getService('user');
    
    if (!$user->can('admin'))
    {
      return '';
    }
    
    $helper = $this->serviceContainer->getService('helper');
    
    $cacheKey = sfConfig::get('sf_cache') ? $user->getCacheHash() : null;
    
    $html = '';
    
    if (sfConfig::get('dm_pageBar_enabled', true) && $user->can('page_bar_admin'))
    {
      $html .= $helper->renderPartial('dmInterface', 'pageBar', array('cacheKey' => $cacheKey));
    }
    
    if (sfConfig::get('dm_mediaBar_enabled', true) && $user->can('media_bar_admin'))
    {
      $html .= $helper->renderPartial('dmInterface', 'mediaBar', array('cacheKey' => $cacheKey));
    }
    
    if ($user->can('tool_bar_admin'))
    {
      $html .= $helper->renderComponent('dmInterface', 'toolBar', array('cacheKey' => $cacheKey));
    }
    
    return $html;
  }

  protected function getJavascriptConfig()
  {
    $config = parent::getJavascriptConfig();
    
    $config['record_id'] = $this->serviceContainer->getService('request')->getParameter('pk', 0);

    return $config;
  }

}