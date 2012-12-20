<?php

/**
 * Behavior for adding gallery features to your models
 *
 * @package     Doctrine
 * @subpackage  Template
 */
class Doctrine_DmGallery extends Doctrine_Record_Generator
{

  public function __construct(array $options = array())
  {
    $this->_options = $options;
  }
  
  public function setTableDefinition()
  {
    $this->setColumnOption('id', 'autoincrement', true);

    $this->hasColumn('dm_media_id', 'integer', null, array('notnull' => true));

    $identifier = $this->_options['table']->getIdentifier();
    if (is_array($identifier))
    {
      throw new dmException('DmGallery works with unique identifier tables only');
    }
    $identifierDefinition = $this->_options['table']->getColumnDefinition($identifier);
    $unsigned = isset($identifierDefinition['unsigned']) ? $identifierDefinition['unsigned'] : false;

    $this->hasColumn('dm_record_id', $identifierDefinition['type'], $identifierDefinition['length'], array('notnull' => true, 'unsigned' => $unsigned));

    $this->hasColumn('position', 'integer');

    $this->index('record_dm_media', array('fields' => array('dm_record_id', 'dm_media_id'), 'type' => 'unique'));
    $this->option('symfony', array('form' => false, 'filter' => false));

    $this->addListener(new Doctrine_Template_Listener_Sortable(array('new' => isset($this->_options['new']) ? $this->_options['new'] : 'first')));
    
  }
  
  public function generateClass(array $definition = array())
  {
    $definition['inheritance']['extends'] = 'myDoctrineRecord';

    return parent::generateClass($definition);
  }
  
  public function buildRelation()
  {
    $this->_table->bind(array($this->_options['mediaClass'], array(
        'local'    => 'dm_media_id',
        'foreign'  => 'id',
        'onDelete' => 'CASCADE'
      )), Doctrine_Relation::ONE);

    $this->_table->bind(array($this->getOption('table')->getComponentName(), array(
        'local'    => 'dm_record_id',
        'foreign'  => 'id',
        'onDelete' => 'CASCADE'
      )), Doctrine_Relation::ONE);

    $this->getOption('table')->bind(array($this->_options['mediaClass'] . ' as ' . $this->_options['mediaAlias'], array(
        'local'    => 'dm_record_id',
        'foreign'  => 'dm_media_id',
        'refClass' => $this->_table->getComponentName(),
        'orderBy'  => 'position ASC'
      )), Doctrine_Relation::MANY);
//      parent::buildRelation();
  }

  public function setUp()
  {

  }
}