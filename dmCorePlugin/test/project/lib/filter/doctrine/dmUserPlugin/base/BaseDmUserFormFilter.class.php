<?php

/**
 * DmUser filter form base class.
 *
 * @package    retest
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class BaseDmUserFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {


		if($this->needsWidget('id')){
			$this->setWidget('id', new sfWidgetFormDmFilterInput());
			$this->setValidator('id', new sfValidatorDoctrineChoice(array('required' => false, 'model' => 'DmUser', 'column' => 'id')));
		}
		if($this->needsWidget('username')){
			$this->setWidget('username', new sfWidgetFormDmFilterInput());
			$this->setValidator('username', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('email')){
			$this->setWidget('email', new sfWidgetFormDmFilterInput());
			$this->setValidator('email', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('algorithm')){
			$this->setWidget('algorithm', new sfWidgetFormDmFilterInput());
			$this->setValidator('algorithm', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('salt')){
			$this->setWidget('salt', new sfWidgetFormDmFilterInput());
			$this->setValidator('salt', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('password')){
			$this->setWidget('password', new sfWidgetFormDmFilterInput());
			$this->setValidator('password', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
		}
		if($this->needsWidget('is_active')){
			$this->setWidget('is_active', new sfWidgetFormChoice(array('choices' => array('' => $this->getI18n()->__('yes or no', array(), 'dm'), 1 => $this->getI18n()->__('yes', array(), 'dm'), 0 => $this->getI18n()->__('no', array(), 'dm')))));
			$this->setValidator('is_active', new sfValidatorBoolean());
		}
		if($this->needsWidget('is_super_admin')){
			$this->setWidget('is_super_admin', new sfWidgetFormChoice(array('choices' => array('' => $this->getI18n()->__('yes or no', array(), 'dm'), 1 => $this->getI18n()->__('yes', array(), 'dm'), 0 => $this->getI18n()->__('no', array(), 'dm')))));
			$this->setValidator('is_super_admin', new sfValidatorBoolean());
		}
		if($this->needsWidget('last_login')){
			$this->setWidget('last_login', new sfWidgetFormChoice(array('choices' => array(
        ''      => '',
        'today' => $this->getI18n()->__('Today'),
        'week'  => $this->getI18n()->__('Past %number% days', array('%number%' => 7)),
        'month' => $this->getI18n()->__('This month'),
        'year'  => $this->getI18n()->__('This year')
      ))));
			$this->setValidator('last_login', new sfValidatorChoice(array('required' => false, 'choices' => array_keys($this->widgetSchema['last_login']->getOption('choices')))));
		}
		if($this->needsWidget('forgot_password_code')){
			$this->setWidget('forgot_password_code', new sfWidgetFormDmFilterInput());
			$this->setValidator('forgot_password_code', new sfValidatorSchemaFilter('text', new sfValidatorString(array('required' => false))));
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

		if($this->needsWidget('groups_list')){
			$this->setWidget('groups_list', new sfWidgetFormDmDoctrineChoice(array('multiple' => true, 'model' => 'DmGroup', 'expanded' => true)));
			$this->setValidator('groups_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmGroup', 'required' => false)));
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

		if($this->needsWidget('dm_lock_list')){
			$this->setWidget('dm_lock_list', new sfWidgetFormDmDoctrineChoice(array('multiple' => true, 'model' => 'DmLock', 'expanded' => true)));
			$this->setValidator('dm_lock_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmLock', 'required' => false)));
		}
		if($this->needsWidget('posts_list')){
			$this->setWidget('posts_list', new sfWidgetFormDmDoctrineChoice(array('multiple' => true, 'model' => 'DmTestPost', 'expanded' => true)));
			$this->setValidator('posts_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmTestPost', 'required' => false)));
		}
		if($this->needsWidget('dm_test_posts_list')){
			$this->setWidget('dm_test_posts_list', new sfWidgetFormDmDoctrineChoice(array('multiple' => true, 'model' => 'DmTestPost', 'expanded' => true)));
			$this->setValidator('dm_test_posts_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmTestPost', 'required' => false)));
		}
		if($this->needsWidget('created_dm_test_fruits_list')){
			$this->setWidget('created_dm_test_fruits_list', new sfWidgetFormDmDoctrineChoice(array('multiple' => true, 'model' => 'DmTestFruit', 'expanded' => true)));
			$this->setValidator('created_dm_test_fruits_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmTestFruit', 'required' => false)));
		}
		if($this->needsWidget('updated_dm_test_fruits_list')){
			$this->setWidget('updated_dm_test_fruits_list', new sfWidgetFormDmDoctrineChoice(array('multiple' => true, 'model' => 'DmTestFruit', 'expanded' => true)));
			$this->setValidator('updated_dm_test_fruits_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmTestFruit', 'required' => false)));
		}
		if($this->needsWidget('created_dm_test_domain_translations_list')){
			$this->setWidget('created_dm_test_domain_translations_list', new sfWidgetFormDmDoctrineChoice(array('multiple' => true, 'model' => 'DmTestDomainTranslation', 'expanded' => true)));
			$this->setValidator('created_dm_test_domain_translations_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmTestDomainTranslation', 'required' => false)));
		}
		if($this->needsWidget('updated_dm_test_domain_translations_list')){
			$this->setWidget('updated_dm_test_domain_translations_list', new sfWidgetFormDmDoctrineChoice(array('multiple' => true, 'model' => 'DmTestDomainTranslation', 'expanded' => true)));
			$this->setValidator('updated_dm_test_domain_translations_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmTestDomainTranslation', 'required' => false)));
		}
		if($this->needsWidget('dm_user_group_list')){
			$this->setWidget('dm_user_group_list', new sfWidgetFormDmDoctrineChoice(array('multiple' => true, 'model' => 'DmUserGroup', 'expanded' => true)));
			$this->setValidator('dm_user_group_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmUserGroup', 'required' => false)));
		}
		if($this->needsWidget('dm_user_permission_list')){
			$this->setWidget('dm_user_permission_list', new sfWidgetFormDmDoctrineChoice(array('multiple' => true, 'model' => 'DmUserPermission', 'expanded' => true)));
			$this->setValidator('dm_user_permission_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmUserPermission', 'required' => false)));
		}
		if($this->needsWidget('dm_record_permission_user_list')){
			$this->setWidget('dm_record_permission_user_list', new sfWidgetFormDmDoctrineChoice(array('multiple' => true, 'model' => 'DmRecordPermissionUser', 'expanded' => true)));
			$this->setValidator('dm_record_permission_user_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmRecordPermissionUser', 'required' => false)));
		}
		if($this->needsWidget('dm_record_permission_association_user_list')){
			$this->setWidget('dm_record_permission_association_user_list', new sfWidgetFormDmDoctrineChoice(array('multiple' => true, 'model' => 'DmRecordPermissionAssociationUser', 'expanded' => true)));
			$this->setValidator('dm_record_permission_association_user_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmRecordPermissionAssociationUser', 'required' => false)));
		}


    

    $this->widgetSchema->setNameFormat('dm_user_filters[%s]');

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

    $query->leftJoin('r.DmUserGroup DmUserGroup')
          ->andWhereIn('DmUserGroup.dm_group_id', $values);
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

    $query->leftJoin('r.DmUserPermission DmUserPermission')
          ->andWhereIn('DmUserPermission.dm_permission_id', $values);
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

    $query->leftJoin('r.DmRecordPermissionUser DmRecordPermissionUser')
          ->andWhereIn('DmRecordPermissionUser.dm_record_permission_id', $values);
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

    $query->leftJoin('r.DmRecordPermissionAssociationUser DmRecordPermissionAssociationUser')
          ->andWhereIn('DmRecordPermissionAssociationUser.dm_record_permission_association_id', $values);
  }

  public function getModelName()
  {
    return 'DmUser';
  }

  public function getFields()
  {
    return array(
      'id'                                    => 'Number',
      'username'                              => 'Text',
      'email'                                 => 'Text',
      'algorithm'                             => 'Text',
      'salt'                                  => 'Text',
      'password'                              => 'Text',
      'is_active'                             => 'Boolean',
      'is_super_admin'                        => 'Boolean',
      'last_login'                            => 'Date',
      'forgot_password_code'                  => 'Text',
      'created_at'                            => 'Date',
      'updated_at'                            => 'Date',
      'groups_list'                           => 'ManyKey',
      'permissions_list'                      => 'ManyKey',
      'records_list'                          => 'ManyKey',
      'records_permissions_associations_list' => 'ManyKey',
    );
  }
}
