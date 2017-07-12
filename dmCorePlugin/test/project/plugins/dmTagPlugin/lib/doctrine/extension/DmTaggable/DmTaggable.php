<?php

class Doctrine_DmTaggable extends Doctrine_Record_Generator
{
  protected $_options = array();

  public function __construct(array $options = array())
  {
    $this->_options = $options;
  }

  public function setTableDefinition()
  {
    $this->hasColumn('dm_tag_id', 'integer', null, array('primary' => true));
    
    $this->option('symfony', array('form' => false, 'filter' => false));
  }

  public function generateClass(array $definition = array())
  {
    $definition['inheritance']['extends'] = 'myDoctrineRecord';

    return parent::generateClass($definition);
  }

  public function buildRelation()
  {
    $this->_table->bind(array($this->_options['tagClass'], array(
      'local'    => 'dm_tag_id',
      'foreign'  => 'id',
      'onDelete' => 'CASCADE'
    )), Doctrine_Relation::ONE);

    $this->getOption('table')->bind(array($this->_options['tagClass'] . ' as ' . $this->_options['tagAlias'], array(
      'local'    => 'id',
      'foreign'  => 'dm_tag_id',
      'refClass' => $this->_table->getComponentName()
    )), Doctrine_Relation::MANY);

    parent::buildRelation();
  }

  public function setUp()
  {
  }
}