<?php

/**
 * DmWidget form base class.
 *
 * @method DmWidget getObject() Returns the current form's model object
 *
 * @package    retest
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id$
 */
abstract class BaseDmWidgetForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'         => new sfWidgetFormInputHidden(),
      'dm_zone_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Zone'), 'add_empty' => false)),
      'module'     => new sfWidgetFormInputText(),
      'action'     => new sfWidgetFormInputText(),
      'css_class'  => new sfWidgetFormInputText(),
      'position'   => new sfWidgetFormInputText(),
      'updated_at' => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'         => new sfValidatorDoctrineChoice(array('model' => $this->getModelName(), 'column' => 'id', 'required' => false)),
      'dm_zone_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Zone'))),
      'module'     => new sfValidatorString(array('max_length' => 127)),
      'action'     => new sfValidatorString(array('max_length' => 127)),
      'css_class'  => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'position'   => new sfValidatorInteger(array('required' => false)),
      'updated_at' => new sfValidatorDateTime(),
    ));

		if('embed' == sfConfig::get('dm_i18n_form'))
    {
      $this->embedI18n(sfConfig::get('dm_i18n_cultures'));
    }
    else
    {
      $this->mergeI18nForm();
    }

    $this->widgetSchema->setNameFormat('dm_widget[%s]');

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
    return 'DmWidget';
  }

}