<?php

class dmAdminFullPageTreeView extends dmAdminPageTreeView
{

  protected function renderOpenLi(array $page)
  {
    if($page[1] === 'show')
    {
      $type = 'auto';
    }
    else
    {
      $type = $this->lastLevel === false ? 'root' : 'manual';
    }
    
    return '<li id="dmp'.$page[0].'" rel="'.$type.'">';
  }
  
}