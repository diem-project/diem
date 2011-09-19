<?php

/**
 * DmPermission form base class.
 *
 * @method DmPermission getObject() Returns the current form's model object
 *
 * @package    retest
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id$
 * @generator  Diem 5.4.0-DEV
 */
abstract class BaseDmPermissionForm extends BaseFormDoctrine
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
			$this->setValidator('description', new sfValidatorString(array('max_length' => 5000, 'required' => false)));
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
		if($this->needsWidget('groups_list')){
			$this->setWidget('groups_list', new sfWidgetFormDmPaginatedDoctrineChoice(array('multiple' => true, 'model' => 'DmGroup', 'expanded' => true)));
			$this->setValidator('groups_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmGroup', 'required' => false)));
		}

		//one to many
		if($this->needsWidget('dm_user_permission_list')){
			$this->setWidget('dm_user_permission_list', new sfWidgetFormDmPaginatedDoctrineChoice(array('multiple' => true, 'model' => 'DmUserPermission', 'expanded' => true)));
			$this->setValidator('dm_user_permission_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmUserPermission', 'required' => false)));
		}
		//one to many
		if($this->needsWidget('dm_group_permission_list')){
			$this->setWidget('dm_group_permission_list', new sfWidgetFormDmPaginatedDoctrineChoice(array('multiple' => true, 'model' => 'DmGroupPermission', 'expanded' => true)));
			$this->setValidator('dm_group_permission_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmGroupPermission', 'required' => false)));
		}





    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'DmPermission', 'column' => array('name')))
    );

    $this->widgetSchema->setNameFormat('dm_permission[%s]');

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
    return 'DmPermission';
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

    if (isset($this->widgetSchema['dm_user_permission_list']))
    {
        $this->setDefault('dm_user_permission_list', array_merge((array)$this->getDefault('dm_user_permission_list'),$this->object->DmUserPermission->getPrimaryKeys()));
    }

    if (isset($this->widgetSchema['dm_group_permission_list']))
    {
        $this->setDefault('dm_group_permission_list', array_merge((array)$this->getDefault('dm_group_permission_list'),$this->object->DmGroupPermission->getPrimaryKeys()));
    }

  }

  protected function doSave($con = null)
  {
    $this->saveUsersList($con);
    $this->saveGroupsList($con);
    $this->saveDmUserPermissionList($con);
    $this->saveDmGroupPermissionList($con);

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

}
