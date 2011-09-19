<?php

/**
 * DmGroup filter form base class.
 *
 * @package    retest
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class BaseDmGroupFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {


		if($this->needsWidget('id')){
			$this->setWidget('id', new sfWidgetFormDmFilterInput());
			$this->setValidator('id', new sfValidatorDoctrineChoice(array('required' => false, 'model' => 'DmGroup', 'column' => 'id')));
		}
		if($this->needsWidget('name')){
			$this->setWidget('name', new sfWidgetFormDmFilterInput());
			$this->setValidator('name', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('description')){
			$this->setWidget('description', new sfWidgetFormDmFilterInput());
			$this->setValidator('description', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('created_at')){
			$this->setWidget('created_at', new sfWidgetFormChoice(array('choices' => array(
        ''      => '',
        'today' => $this->getI18n()->__('Today'),
        'week'  => $this->getI18n()->__('Past %number% days', array('%number%' => 7)),
        'month' => $this->getI18n()->__('This month'),
        'year'  => $this->getI18n()->__('This year')
      ))));
			$this->setValidator('created_at', new sfValidatorChoice(array('required' => false, 'choices' => array_keys($this->widgetSchema['created_at']->getOption('choices')))));
		}
		if($this->needsWidget('updated_at')){
			$this->setWidget('updated_at', new sfWidgetFormChoice(array('choices' => array(
        ''      => '',
        'today' => $this->getI18n()->__('Today'),
        'week'  => $this->getI18n()->__('Past %number% days', array('%number%' => 7)),
        'month' => $this->getI18n()->__('This month'),
        'year'  => $this->getI18n()->__('This year')
      ))));
			$this->setValidator('updated_at', new sfValidatorChoice(array('required' => false, 'choices' => array_keys($this->widgetSchema['updated_at']->getOption('choices')))));
		}

		if($this->needsWidget('users_list')){
			$this->setWidget('users_list', new sfWidgetFormDmDoctrineChoice(array('multiple' => true, 'model' => 'DmUser', 'expanded' => true)));
			$this->setValidator('users_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmUser', 'required' => false)));
		}
		if($this->needsWidget('permissions_list')){
			$this->setWidget('permissions_list', new sfWidgetFormDmDoctrineChoice(array('multiple' => true, 'model' => 'DmPermission', 'expanded' => true)));
			$this->setValidator('permissions_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmPermission', 'required' => false)));
		}
		if($this->needsWidget('records_list')){
			$this->setWidget('records_list', new sfWidgetFormDmDoctrineChoice(array('multiple' => true, 'model' => 'DmRecordPermission', 'expanded' => true)));
			$this->setValidator('records_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmRecordPermission', 'required' => false)));
		}
		if($this->needsWidget('records_permissions_associations_list')){
			$this->setWidget('records_permissions_associations_list', new sfWidgetFormDmDoctrineChoice(array('multiple' => true, 'model' => 'DmRecordPermissionAssociation', 'expanded' => true)));
			$this->setValidator('records_permissions_associations_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmRecordPermissionAssociation', 'required' => false)));
		}

		if($this->needsWidget('dm_user_group_list')){
			$this->setWidget('dm_user_group_list', new sfWidgetFormDmDoctrineChoice(array('multiple' => true, 'model' => 'DmUserGroup', 'expanded' => true)));
			$this->setValidator('dm_user_group_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmUserGroup', 'required' => false)));
		}
		if($this->needsWidget('dm_group_permission_list')){
			$this->setWidget('dm_group_permission_list', new sfWidgetFormDmDoctrineChoice(array('multiple' => true, 'model' => 'DmGroupPermission', 'expanded' => true)));
			$this->setValidator('dm_group_permission_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmGroupPermission', 'required' => false)));
		}
		if($this->needsWidget('dm_record_permission_group_list')){
			$this->setWidget('dm_record_permission_group_list', new sfWidgetFormDmDoctrineChoice(array('multiple' => true, 'model' => 'DmRecordPermissionGroup', 'expanded' => true)));
			$this->setValidator('dm_record_permission_group_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmRecordPermissionGroup', 'required' => false)));
		}
		if($this->needsWidget('dm_record_permission_association_group_list')){
			$this->setWidget('dm_record_permission_association_group_list', new sfWidgetFormDmDoctrineChoice(array('multiple' => true, 'model' => 'DmRecordPermissionAssociationGroup', 'expanded' => true)));
			$this->setValidator('dm_record_permission_association_group_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmRecordPermissionAssociationGroup', 'required' => false)));
		}


    

    $this->widgetSchema->setNameFormat('dm_group_filters[%s]');

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

    $query->leftJoin('r.DmUserGroup DmUserGroup')
          ->andWhereIn('DmUserGroup.dm_user_id', $values);
  }

  public function addPermissionsListColumnQuery(Doctrine_Query $query, $field, $values)
  {
    if (!is_array($values))
    {
      $values = array($values);
    }

    if (!count($values))
    {
      return;
    }

    $query->leftJoin('r.DmGroupPermission DmGroupPermission')
          ->andWhereIn('DmGroupPermission.dm_permission_id', $values);
  }

  public function addRecordsListColumnQuery(Doctrine_Query $query, $field, $values)
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
          ->andWhereIn('DmRecordPermissionGroup.dm_record_permission_id', $values);
  }

  public function addRecordsPermissionsAssociationsListColumnQuery(Doctrine_Query $query, $field, $values)
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
          ->andWhereIn('DmRecordPermissionAssociationGroup.dm_record_permission_association_id', $values);
  }

  public function getModelName()
  {
    return 'DmGroup';
  }

  public function getFields()
  {
    return array(
      'id'                                    => 'Number',
      'name'                                  => 'Text',
      'description'                           => 'Text',
      'created_at'                            => 'Date',
      'updated_at'                            => 'Date',
      'users_list'                            => 'ManyKey',
      'permissions_list'                      => 'ManyKey',
      'records_list'                          => 'ManyKey',
      'records_permissions_associations_list' => 'ManyKey',
    );
  }
}
