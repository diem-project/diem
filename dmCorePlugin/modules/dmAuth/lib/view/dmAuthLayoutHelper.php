<?php

class dmAuthLayoutHelper extends dmCoreLayoutHelper
{

  public function renderMetas()
  {
  	return sprintf('<title>%s</title>', $this->response->getTitle());
  }
  
  public function renderBodyTag($class = null)
  {
    return sprintf('<body class="dm%s">',
      $class ? ' '.$class : ''
    );
  }
}