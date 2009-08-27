<?php

class dmWidgetContentTextView extends dmWidgetContentMediaView
{

  public function configure()
  {
    parent::configure();

    $this->removeRequiredVar(array('mediaId', 'method'));
  }

  public function getViewVars(array $vars = array())
  {
    $vars = parent::getViewVars($vars);
  	
  	if (!empty($vars['mediaId']))
  	{
      $vars['mediaClass'] = '';
      $vars['mediaPosition'] = 'top';
  	}

  	$vars['titlePosition'] = 'outside';
  	
  	$vars['style'] = 'default';
    
    if(!isset($vars['title']))
    {
      $vars['title'] = null;
    }
    
    if(!isset($vars['text']))
    {
      $vars['text'] = null;
    }

    return $vars;
  }
}