<?php

class dmWidgetContentTitleView extends dmWidgetPluginView
{

  public function configure()
  {
    parent::configure();

    $this->addRequiredVar(array('text', 'tag'));
  }
  
  protected function filterViewVars(array $vars = array())
  {
    $vars = parent::filterViewVars($vars);
    
    $vars['text'] = nl2br($vars['text']);
    
    return $vars;
  }

  protected function doRender()
  {
    $vars = $this->getViewVars();
    
    return $this->getHelper()->£($vars['tag'], array('class' => $vars['cssClass']), $vars['text']);
  }
  
  protected function doRenderForIndex()
  {
    return $this->compiledVars['text'];
  }
}