<?php

/**
 * DmPageView form base class.
 *
 * @method DmPageView getObject() Returns the current form's model object
 *
 * @package    retest
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id$
 */
abstract class BaseDmPageViewForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'           => new sfWidgetFormInputHidden(),
      'module'       => new sfWidgetFormInputText(),
      'action'       => new sfWidgetFormInputText(),
      'dm_layout_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Layout'), 'add_empty' => true)),

    ));

    $this->setValidators(array(
      'id'           => new sfValidatorDoctrineChoice(array('model' => $this->getModelName(), 'column' => 'id', 'required' => false)),
      'module'       => new sfValidatorString(array('max_length' => 127)),
      'action'       => new sfValidatorString(array('max_length' => 127)),
      'dm_layout_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Layout'), 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'DmPageView', 'column' => array('module', 'action')))
    );

    $this->widgetSchema->setNameFormat('dm_page_view[%s]');

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
    return 'DmPageView';
  }

}