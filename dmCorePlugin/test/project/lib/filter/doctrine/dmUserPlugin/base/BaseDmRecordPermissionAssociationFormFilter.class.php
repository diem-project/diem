<?php

/**
 * DmRecordPermissionAssociation filter form base class.
 *
 * @package    retest
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class BaseDmRecordPermissionAssociationFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {


		if($this->needsWidget('id')){
			$this->setWidget('id', new sfWidgetFormDmFilterInput());
			$this->setValidator('id', new sfValidatorDoctrineChoice(array('required' => false, 'model' => 'DmRecordPermissionAssociation', 'column' => 'id')));
		}
		if($this->needsWidget('dm_secure_action')){
			$this->setWidget('dm_secure_action', new sfWidgetFormDmFilterInput());
			$this->setValidator('dm_secure_action', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('dm_secure_module')){
			$this->setWidget('dm_secure_module', new sfWidgetFormDmFilterInput());
			$this->setValidator('dm_secure_module', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('dm_secure_model')){
			$this->setWidget('dm_secure_model', new sfWidgetFormDmFilterInput());
			$this->setValidator('dm_secure_model', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}

		if($this->needsWidget('groups_list')){
			$this->setWidget('groups_list', new sfWidgetFormDmDoctrineChoice(array('multiple' => true, 'model' => 'DmGroup', 'expanded' => true)));
			$this->setValidator('groups_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmGroup', 'required' => false)));
		}
		if($this->needsWidget('users_list')){
			$this->setWidget('users_list', new sfWidgetFormDmDoctrineChoice(array('multiple' => true, 'model' => 'DmUser', 'expanded' => true)));
			$this->setValidator('users_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmUser', 'required' => false)));
		}

		if($this->needsWidget('dm_record_permission_association_group_list')){
			$this->setWidget('dm_record_permission_association_group_list', new sfWidgetFormDmDoctrineChoice(array('multiple' => true, 'model' => 'DmRecordPermissionAssociationGroup', 'expanded' => true)));
			$this->setValidator('dm_record_permission_association_group_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmRecordPermissionAssociationGroup', 'required' => false)));
		}
		if($this->needsWidget('dm_record_permission_association_user_list')){
			$this->setWidget('dm_record_permission_association_user_list', new sfWidgetFormDmDoctrineChoice(array('multiple' => true, 'model' => 'DmRecordPermissionAssociationUser', 'expanded' => true)));
			$this->setValidator('dm_record_permission_association_user_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmRecordPermissionAssociationUser', 'required' => false)));
		}


    

    $this->widgetSchema->setNameFormat('dm_record_permission_association_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
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

    $query->leftJoin('r.DmRecordPermissionAssociationGroup DmRecordPermissionAssociationGroup')
          ->andWhereIn('DmRecordPermissionAssociationGroup.dm_group_id', $values);
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

    $query->leftJoin('r.DmRecordPermissionAssociationUser DmRecordPermissionAssociationUser')
          ->andWhereIn('DmRecordPermissionAssociationUser.dm_user_id', $values);
  }

  public function getModelName()
  {
    return 'DmRecordPermissionAssociation';
  }

  public function getFields()
  {
    return array(
      'id'               => 'Number',
      'dm_secure_action' => 'Text',
      'dm_secure_module' => 'Text',
      'dm_secure_model'  => 'Text',
      'groups_list'      => 'ManyKey',
      'users_list'       => 'ManyKey',
    );
  }
}
