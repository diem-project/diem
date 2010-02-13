<?php

abstract class PluginDmPermission extends BaseDmPermission
{

  public function __toString()
  {
    return $this->get('name').' : '.$this->get('description');
  }

}