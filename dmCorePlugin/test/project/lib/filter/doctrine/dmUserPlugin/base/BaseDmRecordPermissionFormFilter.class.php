<?php

/**
 * DmRecordPermission filter form base class.
 *
 * @package    retest
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class BaseDmRecordPermissionFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {


		if($this->needsWidget('id')){
			$this->setWidget('id', new sfWidgetFormDmFilterInput());
			$this->setValidator('id', new sfValidatorDoctrineChoice(array('required' => false, 'model' => 'DmRecordPermission', 'column' => 'id')));
		}
		if($this->needsWidget('secure_module')){
			$this->setWidget('secure_module', new sfWidgetFormDmFilterInput());
			$this->setValidator('secure_module', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('secure_action')){
			$this->setWidget('secure_action', new sfWidgetFormDmFilterInput());
			$this->setValidator('secure_action', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('secure_model')){
			$this->setWidget('secure_model', new sfWidgetFormDmFilterInput());
			$this->setValidator('secure_model', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('secure_record')){
			$this->setWidget('secure_record', new sfWidgetFormDmFilterInput());
			$this->setValidator('secure_record', new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))));
		}
		if($this->needsWidget('description')){
			$this->setWidget('description', new sfWidgetFormDmFilterInput());
			$this->setValidator('description', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}

		if($this->needsWidget('users_list')){
			$this->setWidget('users_list', new sfWidgetFormDmDoctrineChoice(array('multiple' => true, 'model' => 'DmUser', 'expanded' => true)));
			$this->setValidator('users_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmUser', 'required' => false)));
		}
		if($this->needsWidget('groups_list')){
			$this->setWidget('groups_list', new sfWidgetFormDmDoctrineChoice(array('multiple' => true, 'model' => 'DmGroup', 'expanded' => true)));
			$this->setValidator('groups_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmGroup', 'required' => false)));
		}

		if($this->needsWidget('dm_record_permission_user_list')){
			$this->setWidget('dm_record_permission_user_list', new sfWidgetFormDmDoctrineChoice(array('multiple' => true, 'model' => 'DmRecordPermissionUser', 'expanded' => true)));
			$this->setValidator('dm_record_permission_user_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmRecordPermissionUser', 'required' => false)));
		}
		if($this->needsWidget('dm_record_permission_group_list')){
			$this->setWidget('dm_record_permission_group_list', new sfWidgetFormDmDoctrineChoice(array('multiple' => true, 'model' => 'DmRecordPermissionGroup', 'expanded' => true)));
			$this->setValidator('dm_record_permission_group_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmRecordPermissionGroup', 'required' => false)));
		}


    

    $this->widgetSchema->setNameFormat('dm_record_permission_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function addUsersListColumnQuery(Doctrine_Query $query, $field, $values)
  {
    if (!is_array($values))
    {
      $values = array($values);
    }

    if (!count($values))
    {
      return;
    }

    $query->leftJoin('r.DmRecordPermissionUser DmRecordPermissionUser')
          ->andWhereIn('DmRecordPermissionUser.dm_user_id', $values);
  }

  public function addGroupsListColumnQuery(Doctrine_Query $query, $field, $values)
  {
    if (!is_array($values))
    {
      $values = array($values);
    }

    if (!count($values))
    {
      return;
    }

    $query->leftJoin('r.DmRecordPermissionGroup DmRecordPermissionGroup')
          ->andWhereIn('DmRecordPermissionGroup.dm_group_id', $values);
  }

  public function getModelName()
  {
    return 'DmRecordPermission';
  }

  public function getFields()
  {
    return array(
      'id'            => 'Number',
      'secure_module' => 'Text',
      'secure_action' => 'Text',
      'secure_model'  => 'Text',
      'secure_record' => 'Number',
      'description'   => 'Text',
      'users_list'    => 'ManyKey',
      'groups_list'   => 'ManyKey',
    );
  }
}
