<?php

/**
 * DmUser form base class.
 *
 * @method DmUser getObject() Returns the current form's model object
 *
 * @package    retest
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id$
 * @generator  Diem 5.4.0-DEV
 */
abstract class BaseDmUserForm extends BaseFormDoctrine
{
  public function setup()
  {
    parent::setup();

		//column
		if($this->needsWidget('id')){
			$this->setWidget('id', new sfWidgetFormInputHidden());
			$this->setValidator('id', new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)));
		}
		//column
		if($this->needsWidget('username')){
			$this->setWidget('username', new sfWidgetFormInputText());
			$this->setValidator('username', new sfValidatorString(array('max_length' => 255)));
		}
		//column
		if($this->needsWidget('email')){
			$this->setWidget('email', new sfWidgetFormInputText());
			$this->setValidator('email', new sfValidatorString(array('max_length' => 255)));
		}
		//column
		if($this->needsWidget('algorithm')){
			$this->setWidget('algorithm', new sfWidgetFormInputText());
			$this->setValidator('algorithm', new sfValidatorString(array('max_length' => 128, 'required' => false)));
		}
		//column
		if($this->needsWidget('salt')){
			$this->setWidget('salt', new sfWidgetFormInputText());
			$this->setValidator('salt', new sfValidatorString(array('max_length' => 128, 'required' => false)));
		}
		//column
		if($this->needsWidget('password')){
			$this->setWidget('password', new sfWidgetFormInputText());
			$this->setValidator('password', new sfValidatorString(array('max_length' => 128, 'required' => false)));
		}
		//column
		if($this->needsWidget('is_active')){
			$this->setWidget('is_active', new sfWidgetFormInputCheckbox());
			$this->setValidator('is_active', new sfValidatorBoolean(array('required' => false)));
		}
		//column
		if($this->needsWidget('is_super_admin')){
			$this->setWidget('is_super_admin', new sfWidgetFormInputCheckbox());
			$this->setValidator('is_super_admin', new sfValidatorBoolean(array('required' => false)));
		}
		//column
		if($this->needsWidget('last_login')){
			$this->setWidget('last_login', new sfWidgetFormDateTime());
			$this->setValidator('last_login', new sfValidatorDateTime(array('required' => false)));
		}
		//column
		if($this->needsWidget('forgot_password_code')){
			$this->setWidget('forgot_password_code', new sfWidgetFormInputText());
			$this->setValidator('forgot_password_code', new sfValidatorString(array('max_length' => 12, 'required' => false)));
		}
		//column
		if($this->needsWidget('created_at')){
			$this->setWidget('created_at', new sfWidgetFormDateTime());
			$this->setValidator('created_at', new sfValidatorDateTime());
		}
		//column
		if($this->needsWidget('updated_at')){
			$this->setWidget('updated_at', new sfWidgetFormDateTime());
			$this->setValidator('updated_at', new sfValidatorDateTime());
		}

		//many to many
		if($this->needsWidget('groups_list')){
			$this->setWidget('groups_list', new sfWidgetFormDmPaginatedDoctrineChoice(array('multiple' => true, 'model' => 'DmGroup', 'expanded' => true)));
			$this->setValidator('groups_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmGroup', 'required' => false)));
		}
		//many to many
		if($this->needsWidget('permissions_list')){
			$this->setWidget('permissions_list', new sfWidgetFormDmPaginatedDoctrineChoice(array('multiple' => true, 'model' => 'DmPermission', 'expanded' => true)));
			$this->setValidator('permissions_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmPermission', 'required' => false)));
		}
		//many to many
		if($this->needsWidget('records_list')){
			$this->setWidget('records_list', new sfWidgetFormDmPaginatedDoctrineChoice(array('multiple' => true, 'model' => 'DmRecordPermission', 'expanded' => true)));
			$this->setValidator('records_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmRecordPermission', 'required' => false)));
		}
		//many to many
		if($this->needsWidget('records_permissions_associations_list')){
			$this->setWidget('records_permissions_associations_list', new sfWidgetFormDmPaginatedDoctrineChoice(array('multiple' => true, 'model' => 'DmRecordPermissionAssociation', 'expanded' => true)));
			$this->setValidator('records_permissions_associations_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmRecordPermissionAssociation', 'required' => false)));
		}

		//one to many
		if($this->needsWidget('dm_lock_list')){
			$this->setWidget('dm_lock_list', new sfWidgetFormDmPaginatedDoctrineChoice(array('multiple' => true, 'model' => 'DmLock', 'expanded' => true)));
			$this->setValidator('dm_lock_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmLock', 'required' => false)));
		}
		//one to many
		if($this->needsWidget('posts_list')){
			$this->setWidget('posts_list', new sfWidgetFormDmPaginatedDoctrineChoice(array('multiple' => true, 'model' => 'DmTestPost', 'expanded' => true)));
			$this->setValidator('posts_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmTestPost', 'required' => false)));
		}
		//one to many
		if($this->needsWidget('dm_test_posts_list')){
			$this->setWidget('dm_test_posts_list', new sfWidgetFormDmPaginatedDoctrineChoice(array('multiple' => true, 'model' => 'DmTestPost', 'expanded' => true)));
			$this->setValidator('dm_test_posts_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmTestPost', 'required' => false)));
		}
		//one to many
		if($this->needsWidget('created_dm_test_fruits_list')){
			$this->setWidget('created_dm_test_fruits_list', new sfWidgetFormDmPaginatedDoctrineChoice(array('multiple' => true, 'model' => 'DmTestFruit', 'expanded' => true)));
			$this->setValidator('created_dm_test_fruits_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmTestFruit', 'required' => false)));
		}
		//one to many
		if($this->needsWidget('updated_dm_test_fruits_list')){
			$this->setWidget('updated_dm_test_fruits_list', new sfWidgetFormDmPaginatedDoctrineChoice(array('multiple' => true, 'model' => 'DmTestFruit', 'expanded' => true)));
			$this->setValidator('updated_dm_test_fruits_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmTestFruit', 'required' => false)));
		}
		//one to many
		if($this->needsWidget('created_dm_test_domain_translations_list')){
			$this->setWidget('created_dm_test_domain_translations_list', new sfWidgetFormDmPaginatedDoctrineChoice(array('multiple' => true, 'model' => 'DmTestDomainTranslation', 'expanded' => true)));
			$this->setValidator('created_dm_test_domain_translations_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmTestDomainTranslation', 'required' => false)));
		}
		//one to many
		if($this->needsWidget('updated_dm_test_domain_translations_list')){
			$this->setWidget('updated_dm_test_domain_translations_list', new sfWidgetFormDmPaginatedDoctrineChoice(array('multiple' => true, 'model' => 'DmTestDomainTranslation', 'expanded' => true)));
			$this->setValidator('updated_dm_test_domain_translations_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmTestDomainTranslation', 'required' => false)));
		}
		//one to many
		if($this->needsWidget('dm_user_group_list')){
			$this->setWidget('dm_user_group_list', new sfWidgetFormDmPaginatedDoctrineChoice(array('multiple' => true, 'model' => 'DmUserGroup', 'expanded' => true)));
			$this->setValidator('dm_user_group_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmUserGroup', 'required' => false)));
		}
		//one to many
		if($this->needsWidget('dm_user_permission_list')){
			$this->setWidget('dm_user_permission_list', new sfWidgetFormDmPaginatedDoctrineChoice(array('multiple' => true, 'model' => 'DmUserPermission', 'expanded' => true)));
			$this->setValidator('dm_user_permission_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmUserPermission', 'required' => false)));
		}
		//one to many
		if($this->needsWidget('dm_record_permission_user_list')){
			$this->setWidget('dm_record_permission_user_list', new sfWidgetFormDmPaginatedDoctrineChoice(array('multiple' => true, 'model' => 'DmRecordPermissionUser', 'expanded' => true)));
			$this->setValidator('dm_record_permission_user_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmRecordPermissionUser', 'required' => false)));
		}
		//one to many
		if($this->needsWidget('dm_record_permission_association_user_list')){
			$this->setWidget('dm_record_permission_association_user_list', new sfWidgetFormDmPaginatedDoctrineChoice(array('multiple' => true, 'model' => 'DmRecordPermissionAssociationUser', 'expanded' => true)));
			$this->setValidator('dm_record_permission_association_user_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmRecordPermissionAssociationUser', 'required' => false)));
		}





    $this->validatorSchema->setPostValidator(
      new sfValidatorAnd(array(
        new sfValidatorDoctrineUnique(array('model' => 'DmUser', 'column' => array('username'))),
        new sfValidatorDoctrineUnique(array('model' => 'DmUser', 'column' => array('email'))),
        new sfValidatorDoctrineUnique(array('model' => 'DmUser', 'column' => array('forgot_password_code'))),
      ))
    );

    $this->widgetSchema->setNameFormat('dm_user[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
    
    // Unset automatic fields like 'created_at', 'updated_at', 'position'
    // override this method in your form to keep them
    parent::unsetAutoFields();
  }


  protected function doBind(array $values)
  {
    parent::doBind($values);
  }
  
  public function processValues($values)
  {
    $values = parent::processValues($values);
    return $values;
  }
  
  protected function doUpdateObject($values)
  {
    parent::doUpdateObject($values);
  }

  public function getModelName()
  {
    return 'DmUser';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['groups_list']))
    {
        $this->setDefault('groups_list', array_merge((array)$this->getDefault('groups_list'),$this->object->Groups->getPrimaryKeys()));
    }

    if (isset($this->widgetSchema['permissions_list']))
    {
        $this->setDefault('permissions_list', array_merge((array)$this->getDefault('permissions_list'),$this->object->Permissions->getPrimaryKeys()));
    }

    if (isset($this->widgetSchema['records_list']))
    {
        $this->setDefault('records_list', array_merge((array)$this->getDefault('records_list'),$this->object->Records->getPrimaryKeys()));
    }

    if (isset($this->widgetSchema['records_permissions_associations_list']))
    {
        $this->setDefault('records_permissions_associations_list', array_merge((array)$this->getDefault('records_permissions_associations_list'),$this->object->RecordsPermissionsAssociations->getPrimaryKeys()));
    }

    if (isset($this->widgetSchema['dm_lock_list']))
    {
        $this->setDefault('dm_lock_list', array_merge((array)$this->getDefault('dm_lock_list'),$this->object->DmLock->getPrimaryKeys()));
    }

    if (isset($this->widgetSchema['posts_list']))
    {
        $this->setDefault('posts_list', array_merge((array)$this->getDefault('posts_list'),$this->object->Posts->getPrimaryKeys()));
    }

    if (isset($this->widgetSchema['dm_test_posts_list']))
    {
        $this->setDefault('dm_test_posts_list', array_merge((array)$this->getDefault('dm_test_posts_list'),$this->object->DmTestPosts->getPrimaryKeys()));
    }

    if (isset($this->widgetSchema['created_dm_test_fruits_list']))
    {
        $this->setDefault('created_dm_test_fruits_list', array_merge((array)$this->getDefault('created_dm_test_fruits_list'),$this->object->CreatedDmTestFruits->getPrimaryKeys()));
    }

    if (isset($this->widgetSchema['updated_dm_test_fruits_list']))
    {
        $this->setDefault('updated_dm_test_fruits_list', array_merge((array)$this->getDefault('updated_dm_test_fruits_list'),$this->object->UpdatedDmTestFruits->getPrimaryKeys()));
    }

    if (isset($this->widgetSchema['created_dm_test_domain_translations_list']))
    {
        $this->setDefault('created_dm_test_domain_translations_list', array_merge((array)$this->getDefault('created_dm_test_domain_translations_list'),$this->object->CreatedDmTestDomainTranslations->getPrimaryKeys()));
    }

    if (isset($this->widgetSchema['updated_dm_test_domain_translations_list']))
    {
        $this->setDefault('updated_dm_test_domain_translations_list', array_merge((array)$this->getDefault('updated_dm_test_domain_translations_list'),$this->object->UpdatedDmTestDomainTranslations->getPrimaryKeys()));
    }

    if (isset($this->widgetSchema['dm_user_group_list']))
    {
        $this->setDefault('dm_user_group_list', array_merge((array)$this->getDefault('dm_user_group_list'),$this->object->DmUserGroup->getPrimaryKeys()));
    }

    if (isset($this->widgetSchema['dm_user_permission_list']))
    {
        $this->setDefault('dm_user_permission_list', array_merge((array)$this->getDefault('dm_user_permission_list'),$this->object->DmUserPermission->getPrimaryKeys()));
    }

    if (isset($this->widgetSchema['dm_record_permission_user_list']))
    {
        $this->setDefault('dm_record_permission_user_list', array_merge((array)$this->getDefault('dm_record_permission_user_list'),$this->object->DmRecordPermissionUser->getPrimaryKeys()));
    }

    if (isset($this->widgetSchema['dm_record_permission_association_user_list']))
    {
        $this->setDefault('dm_record_permission_association_user_list', array_merge((array)$this->getDefault('dm_record_permission_association_user_list'),$this->object->DmRecordPermissionAssociationUser->getPrimaryKeys()));
    }

  }

  protected function doSave($con = null)
  {
    $this->saveGroupsList($con);
    $this->savePermissionsList($con);
    $this->saveRecordsList($con);
    $this->saveRecordsPermissionsAssociationsList($con);
    $this->saveDmLockList($con);
    $this->savePostsList($con);
    $this->saveDmTestPostsList($con);
    $this->saveCreatedDmTestFruitsList($con);
    $this->saveUpdatedDmTestFruitsList($con);
    $this->saveCreatedDmTestDomainTranslationsList($con);
    $this->saveUpdatedDmTestDomainTranslationsList($con);
    $this->saveDmUserGroupList($con);
    $this->saveDmUserPermissionList($con);
    $this->saveDmRecordPermissionUserList($con);
    $this->saveDmRecordPermissionAssociationUserList($con);

    parent::doSave($con);
  }

  public function saveGroupsList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['groups_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->Groups->getPrimaryKeys();
    $values = $this->getValue('groups_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('Groups', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('Groups', array_values($link));
    }
  }

  public function savePermissionsList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['permissions_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->Permissions->getPrimaryKeys();
    $values = $this->getValue('permissions_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('Permissions', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('Permissions', array_values($link));
    }
  }

  public function saveRecordsList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['records_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->Records->getPrimaryKeys();
    $values = $this->getValue('records_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('Records', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('Records', array_values($link));
    }
  }

  public function saveRecordsPermissionsAssociationsList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['records_permissions_associations_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->RecordsPermissionsAssociations->getPrimaryKeys();
    $values = $this->getValue('records_permissions_associations_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('RecordsPermissionsAssociations', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('RecordsPermissionsAssociations', array_values($link));
    }
  }

  public function saveDmLockList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['dm_lock_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->DmLock->getPrimaryKeys();
    $values = $this->getValue('dm_lock_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('DmLock', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('DmLock', array_values($link));
    }
  }

  public function savePostsList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['posts_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->Posts->getPrimaryKeys();
    $values = $this->getValue('posts_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('Posts', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('Posts', array_values($link));
    }
  }

  public function saveDmTestPostsList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['dm_test_posts_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->DmTestPosts->getPrimaryKeys();
    $values = $this->getValue('dm_test_posts_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('DmTestPosts', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('DmTestPosts', array_values($link));
    }
  }

  public function saveCreatedDmTestFruitsList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['created_dm_test_fruits_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->CreatedDmTestFruits->getPrimaryKeys();
    $values = $this->getValue('created_dm_test_fruits_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('CreatedDmTestFruits', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('CreatedDmTestFruits', array_values($link));
    }
  }

  public function saveUpdatedDmTestFruitsList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['updated_dm_test_fruits_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->UpdatedDmTestFruits->getPrimaryKeys();
    $values = $this->getValue('updated_dm_test_fruits_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('UpdatedDmTestFruits', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('UpdatedDmTestFruits', array_values($link));
    }
  }

  public function saveCreatedDmTestDomainTranslationsList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['created_dm_test_domain_translations_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->CreatedDmTestDomainTranslations->getPrimaryKeys();
    $values = $this->getValue('created_dm_test_domain_translations_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('CreatedDmTestDomainTranslations', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('CreatedDmTestDomainTranslations', array_values($link));
    }
  }

  public function saveUpdatedDmTestDomainTranslationsList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['updated_dm_test_domain_translations_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->UpdatedDmTestDomainTranslations->getPrimaryKeys();
    $values = $this->getValue('updated_dm_test_domain_translations_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('UpdatedDmTestDomainTranslations', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('UpdatedDmTestDomainTranslations', array_values($link));
    }
  }

  public function saveDmUserGroupList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['dm_user_group_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->DmUserGroup->getPrimaryKeys();
    $values = $this->getValue('dm_user_group_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('DmUserGroup', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('DmUserGroup', array_values($link));
    }
  }

  public function saveDmUserPermissionList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['dm_user_permission_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->DmUserPermission->getPrimaryKeys();
    $values = $this->getValue('dm_user_permission_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('DmUserPermission', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('DmUserPermission', array_values($link));
    }
  }

  public function saveDmRecordPermissionUserList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['dm_record_permission_user_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->DmRecordPermissionUser->getPrimaryKeys();
    $values = $this->getValue('dm_record_permission_user_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('DmRecordPermissionUser', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('DmRecordPermissionUser', array_values($link));
    }
  }

  public function saveDmRecordPermissionAssociationUserList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['dm_record_permission_association_user_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->DmRecordPermissionAssociationUser->getPrimaryKeys();
    $values = $this->getValue('dm_record_permission_association_user_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('DmRecordPermissionAssociationUser', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('DmRecordPermissionAssociationUser', array_values($link));
    }
  }

}
