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
 */
abstract class BaseDmUserForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                   => new sfWidgetFormInputHidden(),
      'username'             => new sfWidgetFormInputText(),
      'email'                => new sfWidgetFormInputText(),
      'algorithm'            => new sfWidgetFormInputText(),
      'salt'                 => new sfWidgetFormInputText(),
      'password'             => new sfWidgetFormInputText(),
      'is_active'            => new sfWidgetFormInputCheckbox(),
      'is_super_admin'       => new sfWidgetFormInputCheckbox(),
      'last_login'           => new sfWidgetFormDateTime(),
      'forgot_password_code' => new sfWidgetFormInputText(),
      'created_at'           => new sfWidgetFormDateTime(),
      'updated_at'           => new sfWidgetFormDateTime(),

        'groups_list'          => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'DmGroup', 'expanded' => true)),
        'permissions_list'     => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'DmPermission', 'expanded' => true)),
    ));

    $this->setValidators(array(
      'id'                   => new sfValidatorDoctrineChoice(array('model' => $this->getModelName(), 'column' => 'id', 'required' => false)),
      'username'             => new sfValidatorString(array('max_length' => 255)),
      'email'                => new sfValidatorString(array('max_length' => 255)),
      'algorithm'            => new sfValidatorString(array('max_length' => 128, 'required' => false)),
      'salt'                 => new sfValidatorString(array('max_length' => 128, 'required' => false)),
      'password'             => new sfValidatorString(array('max_length' => 128, 'required' => false)),
      'is_active'            => new sfValidatorBoolean(array('required' => false)),
      'is_super_admin'       => new sfValidatorBoolean(array('required' => false)),
      'last_login'           => new sfValidatorDateTime(array('required' => false)),
      'forgot_password_code' => new sfValidatorString(array('max_length' => 12, 'required' => false)),
      'created_at'           => new sfValidatorDateTime(),
      'updated_at'           => new sfValidatorDateTime(),
        'groups_list'          => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmGroup', 'required' => false)),
        'permissions_list'     => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmPermission', 'required' => false)),
    ));

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
      $this->setDefault('groups_list', $this->object->Groups->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['permissions_list']))
    {
      $this->setDefault('permissions_list', $this->object->Permissions->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveGroupsList($con);
    $this->savePermissionsList($con);

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

}