<?php

/**
 * DmGroup form base class.
 *
 * @method DmGroup getObject() Returns the current form's model object
 *
 * @package    retest
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id$
 * @generator  Diem 5.4.0-DEV
 */
abstract class BaseDmGroupForm extends BaseFormDoctrine
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
		if($this->needsWidget('name')){
			$this->setWidget('name', new sfWidgetFormInputText());
			$this->setValidator('name', new sfValidatorString(array('max_length' => 255, 'required' => false)));
		}
		//column
		if($this->needsWidget('description')){
			$this->setWidget('description', new sfWidgetFormTextarea());
			$this->setValidator('description', new sfValidatorString(array('max_length' => 1000, 'required' => false)));
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
		if($this->needsWidget('users_list')){
			$this->setWidget('users_list', new sfWidgetFormDmPaginatedDoctrineChoice(array('multiple' => true, 'model' => 'DmUser', 'expanded' => true)));
			$this->setValidator('users_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmUser', 'required' => false)));
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
		if($this->needsWidget('dm_user_group_list')){
			$this->setWidget('dm_user_group_list', new sfWidgetFormDmPaginatedDoctrineChoice(array('multiple' => true, 'model' => 'DmUserGroup', 'expanded' => true)));
			$this->setValidator('dm_user_group_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmUserGroup', 'required' => false)));
		}
		//one to many
		if($this->needsWidget('dm_group_permission_list')){
			$this->setWidget('dm_group_permission_list', new sfWidgetFormDmPaginatedDoctrineChoice(array('multiple' => true, 'model' => 'DmGroupPermission', 'expanded' => true)));
			$this->setValidator('dm_group_permission_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmGroupPermission', 'required' => false)));
		}
		//one to many
		if($this->needsWidget('dm_record_permission_group_list')){
			$this->setWidget('dm_record_permission_group_list', new sfWidgetFormDmPaginatedDoctrineChoice(array('multiple' => true, 'model' => 'DmRecordPermissionGroup', 'expanded' => true)));
			$this->setValidator('dm_record_permission_group_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmRecordPermissionGroup', 'required' => false)));
		}
		//one to many
		if($this->needsWidget('dm_record_permission_association_group_list')){
			$this->setWidget('dm_record_permission_association_group_list', new sfWidgetFormDmPaginatedDoctrineChoice(array('multiple' => true, 'model' => 'DmRecordPermissionAssociationGroup', 'expanded' => true)));
			$this->setValidator('dm_record_permission_association_group_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmRecordPermissionAssociationGroup', 'required' => false)));
		}





    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'DmGroup', 'column' => array('name')))
    );

    $this->widgetSchema->setNameFormat('dm_group[%s]');

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
    return 'DmGroup';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['users_list']))
    {
        $this->setDefault('users_list', array_merge((array)$this->getDefault('users_list'),$this->object->Users->getPrimaryKeys()));
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

    if (isset($this->widgetSchema['dm_user_group_list']))
    {
        $this->setDefault('dm_user_group_list', array_merge((array)$this->getDefault('dm_user_group_list'),$this->object->DmUserGroup->getPrimaryKeys()));
    }

    if (isset($this->widgetSchema['dm_group_permission_list']))
    {
        $this->setDefault('dm_group_permission_list', array_merge((array)$this->getDefault('dm_group_permission_list'),$this->object->DmGroupPermission->getPrimaryKeys()));
    }

    if (isset($this->widgetSchema['dm_record_permission_group_list']))
    {
        $this->setDefault('dm_record_permission_group_list', array_merge((array)$this->getDefault('dm_record_permission_group_list'),$this->object->DmRecordPermissionGroup->getPrimaryKeys()));
    }

    if (isset($this->widgetSchema['dm_record_permission_association_group_list']))
    {
        $this->setDefault('dm_record_permission_association_group_list', array_merge((array)$this->getDefault('dm_record_permission_association_group_list'),$this->object->DmRecordPermissionAssociationGroup->getPrimaryKeys()));
    }

  }

  protected function doSave($con = null)
  {
    $this->saveUsersList($con);
    $this->savePermissionsList($con);
    $this->saveRecordsList($con);
    $this->saveRecordsPermissionsAssociationsList($con);
    $this->saveDmUserGroupList($con);
    $this->saveDmGroupPermissionList($con);
    $this->saveDmRecordPermissionGroupList($con);
    $this->saveDmRecordPermissionAssociationGroupList($con);

    parent::doSave($con);
  }

  public function saveUsersList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['users_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->Users->getPrimaryKeys();
    $values = $this->getValue('users_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('Users', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('Users', array_values($link));
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

  public function saveDmGroupPermissionList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['dm_group_permission_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->DmGroupPermission->getPrimaryKeys();
    $values = $this->getValue('dm_group_permission_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('DmGroupPermission', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('DmGroupPermission', array_values($link));
    }
  }

  public function saveDmRecordPermissionGroupList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['dm_record_permission_group_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->DmRecordPermissionGroup->getPrimaryKeys();
    $values = $this->getValue('dm_record_permission_group_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('DmRecordPermissionGroup', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('DmRecordPermissionGroup', array_values($link));
    }
  }

  public function saveDmRecordPermissionAssociationGroupList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['dm_record_permission_association_group_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->DmRecordPermissionAssociationGroup->getPrimaryKeys();
    $values = $this->getValue('dm_record_permission_association_group_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('DmRecordPermissionAssociationGroup', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('DmRecordPermissionAssociationGroup', array_values($link));
    }
  }

}
