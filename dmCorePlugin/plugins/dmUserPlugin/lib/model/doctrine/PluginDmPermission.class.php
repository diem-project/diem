<?php

abstract class PluginDmPermission extends BaseDmPermission
{
	const NEVER_GRANT_ACCESS = '__NEVER_GRANT_ACCESS__123456789__';
	
  public function __toString()
  {
    return $this->get('name').' : '.$this->get('description');
  }

}