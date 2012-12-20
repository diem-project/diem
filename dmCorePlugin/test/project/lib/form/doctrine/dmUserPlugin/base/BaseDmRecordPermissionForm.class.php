<?php

/**
 * DmRecordPermission form base class.
 *
 * @method DmRecordPermission getObject() Returns the current form's model object
 *
 * @package    retest
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id$
 * @generator  Diem 5.4.0-DEV
 */
abstract class BaseDmRecordPermissionForm extends BaseFormDoctrine
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
		if($this->needsWidget('secure_module')){
			$this->setWidget('secure_module', new sfWidgetFormInputText());
			$this->setValidator('secure_module', new sfValidatorString(array('max_length' => 255)));
		}
		//column
		if($this->needsWidget('secure_action')){
			$this->setWidget('secure_action', new sfWidgetFormInputText());
			$this->setValidator('secure_action', new sfValidatorString(array('max_length' => 255)));
		}
		//column
		if($this->needsWidget('secure_model')){
			$this->setWidget('secure_model', new sfWidgetFormInputText());
			$this->setValidator('secure_model', new sfValidatorString(array('max_length' => 255)));
		}
		//column
		if($this->needsWidget('secure_record')){
			$this->setWidget('secure_record', new sfWidgetFormInputText());
			$this->setValidator('secure_record', new sfValidatorInteger());
		}
		//column
		if($this->needsWidget('description')){
			$this->setWidget('description', new sfWidgetFormTextarea());
			$this->setValidator('description', new sfValidatorString(array('max_length' => 1000, 'required' => false)));
		}

		//many to many
		if($this->needsWidget('users_list')){
			$this->setWidget('users_list', new sfWidgetFormDmPaginatedDoctrineChoice(array('multiple' => true, 'model' => 'DmUser', 'expanded' => true)));
			$this->setValidator('users_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmUser', 'required' => false)));
		}
		//many to many
		if($this->needsWidget('groups_list')){
			$this->setWidget('groups_list', new sfWidgetFormDmPaginatedDoctrineChoice(array('multiple' => true, 'model' => 'DmGroup', 'expanded' => true)));
			$this->setValidator('groups_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmGroup', 'required' => false)));
		}

		//one to many
		if($this->needsWidget('dm_record_permission_user_list')){
			$this->setWidget('dm_record_permission_user_list', new sfWidgetFormDmPaginatedDoctrineChoice(array('multiple' => true, 'model' => 'DmRecordPermissionUser', 'expanded' => true)));
			$this->setValidator('dm_record_permission_user_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmRecordPermissionUser', 'required' => false)));
		}
		//one to many
		if($this->needsWidget('dm_record_permission_group_list')){
			$this->setWidget('dm_record_permission_group_list', new sfWidgetFormDmPaginatedDoctrineChoice(array('multiple' => true, 'model' => 'DmRecordPermissionGroup', 'expanded' => true)));
			$this->setValidator('dm_record_permission_group_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmRecordPermissionGroup', 'required' => false)));
		}





    $this->widgetSchema->setNameFormat('dm_record_permission[%s]');

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
    return 'DmRecordPermission';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['users_list']))
    {
        $this->setDefault('users_list', array_merge((array)$this->getDefault('users_list'),$this->object->Users->getPrimaryKeys()));
    }

    if (isset($this->widgetSchema['groups_list']))
    {
        $this->setDefault('groups_list', array_merge((array)$this->getDefault('groups_list'),$this->object->Groups->getPrimaryKeys()));
    }

    if (isset($this->widgetSchema['dm_record_permission_user_list']))
    {
        $this->setDefault('dm_record_permission_user_list', array_merge((array)$this->getDefault('dm_record_permission_user_list'),$this->object->DmRecordPermissionUser->getPrimaryKeys()));
    }

    if (isset($this->widgetSchema['dm_record_permission_group_list']))
    {
        $this->setDefault('dm_record_permission_group_list', array_merge((array)$this->getDefault('dm_record_permission_group_list'),$this->object->DmRecordPermissionGroup->getPrimaryKeys()));
    }

  }

  protected function doSave($con = null)
  {
    $this->saveUsersList($con);
    $this->saveGroupsList($con);
    $this->saveDmRecordPermissionUserList($con);
    $this->saveDmRecordPermissionGroupList($con);

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

}
