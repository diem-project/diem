<?php

class dmAdminLayoutHelper extends dmCoreLayoutHelper
{

  public function renderBodyTag($options = array())
  {
    $options = dmString::toArray($options);

    $options['class'] = array_merge(dmArray::get($options, 'class', array()), array(
      $this->serviceContainer->getParameter('controller.module').'_'.$this->serviceContainer->getParameter('controller.action')
    ));

    return parent::renderBodyTag($options);
  }
  
  public function renderEditBars()
  {
    $user = $this->getService('user');
    
    if (!$user->can('admin'))
    {
      return '';
    }
    
    $helper = $this->getService('helper');
    
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

    if (sfConfig::get('dm_toolBar_enabled', true) && $user->can('tool_bar_admin'))
    {
      $html .= $helper->renderPartial('dmInterface', 'toolBar', array('cacheKey' => $cacheKey));
    }
    
    return $html;
  }

  protected function getJavascriptConfig()
  {
    $config = parent::getJavascriptConfig();
    
    $config['record_id'] = $this->getService('request')->getParameter('pk', 0);

    return $config;
  }

}