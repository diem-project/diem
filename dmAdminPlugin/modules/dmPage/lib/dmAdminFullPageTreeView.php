<?php

class dmAdminFullPageTreeView extends dmAdminPageTreeView
{

  protected function renderOpenLi(array $page)
  {
    $type = $this->lastLevel === false ? 'root' : 'manual';
    if('show' === $page[2] && $module = $this->moduleManager->getModule($page[1]))
    {
      if($module->hasPage() && !$module->isPageManualPosition())
      {
        $type = 'auto';
      }
    }
    
    return '<li id="dmp'.$page[0].'" rel="'.$type.'">';
  }
  
}
