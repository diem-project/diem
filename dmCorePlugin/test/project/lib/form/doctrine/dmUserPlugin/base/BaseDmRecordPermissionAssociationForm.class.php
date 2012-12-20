<?php

/**
 * DmRecordPermissionAssociation form base class.
 *
 * @method DmRecordPermissionAssociation getObject() Returns the current form's model object
 *
 * @package    retest
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id$
 * @generator  Diem 5.4.0-DEV
 */
abstract class BaseDmRecordPermissionAssociationForm extends BaseFormDoctrine
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
		if($this->needsWidget('dm_secure_action')){
			$this->setWidget('dm_secure_action', new sfWidgetFormInputText());
			$this->setValidator('dm_secure_action', new sfValidatorString(array('max_length' => 255, 'required' => false)));
		}
		//column
		if($this->needsWidget('dm_secure_module')){
			$this->setWidget('dm_secure_module', new sfWidgetFormInputText());
			$this->setValidator('dm_secure_module', new sfValidatorString(array('max_length' => 255, 'required' => false)));
		}
		//column
		if($this->needsWidget('dm_secure_model')){
			$this->setWidget('dm_secure_model', new sfWidgetFormInputText());
			$this->setValidator('dm_secure_model', new sfValidatorString(array('max_length' => 255, 'required' => false)));
		}

		//many to many
		if($this->needsWidget('groups_list')){
			$this->setWidget('groups_list', new sfWidgetFormDmPaginatedDoctrineChoice(array('multiple' => true, 'model' => 'DmGroup', 'expanded' => true)));
			$this->setValidator('groups_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmGroup', 'required' => false)));
		}
		//many to many
		if($this->needsWidget('users_list')){
			$this->setWidget('users_list', new sfWidgetFormDmPaginatedDoctrineChoice(array('multiple' => true, 'model' => 'DmUser', 'expanded' => true)));
			$this->setValidator('users_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmUser', 'required' => false)));
		}

		//one to many
		if($this->needsWidget('dm_record_permission_association_group_list')){
			$this->setWidget('dm_record_permission_association_group_list', new sfWidgetFormDmPaginatedDoctrineChoice(array('multiple' => true, 'model' => 'DmRecordPermissionAssociationGroup', 'expanded' => true)));
			$this->setValidator('dm_record_permission_association_group_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmRecordPermissionAssociationGroup', 'required' => false)));
		}
		//one to many
		if($this->needsWidget('dm_record_permission_association_user_list')){
			$this->setWidget('dm_record_permission_association_user_list', new sfWidgetFormDmPaginatedDoctrineChoice(array('multiple' => true, 'model' => 'DmRecordPermissionAssociationUser', 'expanded' => true)));
			$this->setValidator('dm_record_permission_association_user_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmRecordPermissionAssociationUser', 'required' => false)));
		}





    $this->widgetSchema->setNameFormat('dm_record_permission_association[%s]');

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
    return 'DmRecordPermissionAssociation';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['groups_list']))
    {
        $this->setDefault('groups_list', array_merge((array)$this->getDefault('groups_list'),$this->object->Groups->getPrimaryKeys()));
    }

    if (isset($this->widgetSchema['users_list']))
    {
        $this->setDefault('users_list', array_merge((array)$this->getDefault('users_list'),$this->object->Users->getPrimaryKeys()));
    }

    if (isset($this->widgetSchema['dm_record_permission_association_group_list']))
    {
        $this->setDefault('dm_record_permission_association_group_list', array_merge((array)$this->getDefault('dm_record_permission_association_group_list'),$this->object->DmRecordPermissionAssociationGroup->getPrimaryKeys()));
    }

    if (isset($this->widgetSchema['dm_record_permission_association_user_list']))
    {
        $this->setDefault('dm_record_permission_association_user_list', array_merge((array)$this->getDefault('dm_record_permission_association_user_list'),$this->object->DmRecordPermissionAssociationUser->getPrimaryKeys()));
    }

  }

  protected function doSave($con = null)
  {
    $this->saveGroupsList($con);
    $this->saveUsersList($con);
    $this->saveDmRecordPermissionAssociationGroupList($con);
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
