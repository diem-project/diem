<?php

/**
 * DmTag form base class.
 *
 * @method DmTag getObject() Returns the current form's model object
 *
 * @package    retest
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id$
 */
abstract class BaseDmTagForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                  => new sfWidgetFormInputHidden(),
      'name'                => new sfWidgetFormInputText(),
        'dm_test_fruit_list'  => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'DmTestFruit', 'expanded' => true)),
        'dm_test_domain_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'DmTestDomain', 'expanded' => true)),
    ));

    $this->setValidators(array(
      'id'                  => new sfValidatorDoctrineChoice(array('model' => $this->getModelName(), 'column' => 'id', 'required' => false)),
      'name'                => new sfValidatorString(array('max_length' => 255)),
        'dm_test_fruit_list'  => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmTestFruit', 'required' => false)),
        'dm_test_domain_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmTestDomain', 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'DmTag', 'column' => array('name')))
    );

    $this->widgetSchema->setNameFormat('dm_tag[%s]');

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
    return 'DmTag';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['dm_test_fruit_list']))
    {
      $this->setDefault('dm_test_fruit_list', $this->object->DmTestFruit->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['dm_test_domain_list']))
    {
      $this->setDefault('dm_test_domain_list', $this->object->DmTestDomain->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveDmTestFruitList($con);
    $this->saveDmTestDomainList($con);

    parent::doSave($con);
  }

  public function saveDmTestFruitList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['dm_test_fruit_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->DmTestFruit->getPrimaryKeys();
    $values = $this->getValue('dm_test_fruit_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('DmTestFruit', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('DmTestFruit', array_values($link));
    }
  }

  public function saveDmTestDomainList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['dm_test_domain_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->DmTestDomain->getPrimaryKeys();
    $values = $this->getValue('dm_test_domain_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('DmTestDomain', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('DmTestDomain', array_values($link));
    }
  }

}