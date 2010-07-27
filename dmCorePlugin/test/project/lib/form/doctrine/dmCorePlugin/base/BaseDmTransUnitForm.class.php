<?php

/**
 * DmTransUnit form base class.
 *
 * @method DmTransUnit getObject() Returns the current form's model object
 *
 * @package    retest
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id$
 */
abstract class BaseDmTransUnitForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'              => new sfWidgetFormInputHidden(),
      'dm_catalogue_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('DmCatalogue'), 'add_empty' => false)),
      'source'          => new sfWidgetFormTextarea(),
      'target'          => new sfWidgetFormTextarea(),
      'meta'            => new sfWidgetFormInputText(),
      'created_at'      => new sfWidgetFormDateTime(),
      'updated_at'      => new sfWidgetFormDateTime(),

    ));

    $this->setValidators(array(
      'id'              => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'dm_catalogue_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('DmCatalogue'))),
      'source'          => new sfValidatorString(array('max_length' => 60000)),
      'target'          => new sfValidatorString(array('max_length' => 60000)),
      'meta'            => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'created_at'      => new sfValidatorDateTime(),
      'updated_at'      => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('dm_trans_unit[%s]');

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
    return 'DmTransUnit';
  }

}