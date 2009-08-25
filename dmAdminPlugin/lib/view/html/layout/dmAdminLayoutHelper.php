<?php

class dmAdminLayoutHelper extends dmCoreLayoutHelper
{

  public function renderMetas()
  {
  	return sprintf('<title>%s</title>', $this->response->getTitle());
  }
	
	public function renderBodyTag($class = null)
	{
		return sprintf('<body class="dm%s%s%s%s">',
      $this->dmContext->isListPage() ? ' list' : '',
      $this->dmContext->isFormPage() ? ' form' : '',
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
  	
    $this->dmContext->getSfContext()->getConfiguration()->loadHelpers('Partial');
  
    $html = '';
    
    if (sfConfig::get('dm_pageBar_enabled', true) && $this->user->can('page_bar_admin'))
    {
      $html .= get_partial('dmInterface/pageBar');
    }
    
    if (sfConfig::get('dm_mediaBar_enabled', true) && $this->user->can('media_bar_admin'))
    {
      $html .= get_partial('dmInterface/mediaBar');
    }
    
    if ($this->user->can('tool_bar_admin'))
    {
      $html .= get_component('dmInterface', 'toolBar');
    }
    
    return $html;
  }


}