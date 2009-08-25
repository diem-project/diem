<?php

class dmNestedSet extends Doctrine_Template_NestedSet
{
  /**
   * Set up NestedSet template
   *
   * @return void
   */
  public function setUp()
  {
    $this->_table->setOption('treeOptions', $this->_options);
    $this->_table->setOption('treeImpl', 'dmNestedSet');
  }
}