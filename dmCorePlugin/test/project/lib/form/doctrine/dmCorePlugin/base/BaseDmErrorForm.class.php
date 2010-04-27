<?php

/**
 * DmError form base class.
 *
 * @method DmError getObject() Returns the current form's model object
 *
 * @package    retest
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id$
 */
abstract class BaseDmErrorForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'          => new sfWidgetFormInputHidden(),
      'php_class'   => new sfWidgetFormInputText(),
      'name'        => new sfWidgetFormInputText(),
      'description' => new sfWidgetFormTextarea(),
      'module'      => new sfWidgetFormInputText(),
      'action'      => new sfWidgetFormInputText(),
      'uri'         => new sfWidgetFormInputText(),
      'env'         => new sfWidgetFormInputText(),
      'created_at'  => new sfWidgetFormDateTime(),

    ));

    $this->setValidators(array(
      'id'          => new sfValidatorDoctrineChoice(array('model' => $this->getModelName(), 'column' => 'id', 'required' => false)),
      'php_class'   => new sfValidatorString(array('max_length' => 127)),
      'name'        => new sfValidatorString(array('max_length' => 255)),
      'description' => new sfValidatorString(array('max_length' => 60000, 'required' => false)),
      'module'      => new sfValidatorString(array('max_length' => 127, 'required' => false)),
      'action'      => new sfValidatorString(array('max_length' => 127, 'required' => false)),
      'uri'         => new sfValidatorString(array('max_length' => 255)),
      'env'         => new sfValidatorString(array('max_length' => 63)),
      'created_at'  => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('dm_error[%s]');

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
    return 'DmError';
  }

}