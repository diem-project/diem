<?php

class Doctrine_Template_DmVersionable extends Doctrine_Template_Versionable
{
  /**
   * Setup the Versionable behavior for the template
   *
   * @return void
   */
  public function setUp()
  {
    if ($this->_plugin->getOption('auditLog')) {
      $this->_plugin->initialize($this->_table);
    }

    $version = $this->_options['version'];
    $name = $version['name'] . (isset($version['alias']) ? ' as ' . $version['alias'] : '');
    $this->hasColumn($name, $version['type'], $version['length'], $version['options']);

    $this->addListener(new dmDoctrineAuditLogListener($this->_plugin));
  }
}