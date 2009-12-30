<?php

class dmAuthLayoutHelper extends dmCoreLayoutHelper
{

  public function renderBodyTag($class = null)
  {
    return sprintf('<body class="dm%s">',
      $class ? ' '.$class : ''
    );
  }
}