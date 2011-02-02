<?php

abstract class PluginDmGroup extends BaseDmGroup
{

  public function __toString()
  {
    return $this->get('name').' : '.$this->get('description');
  }

}