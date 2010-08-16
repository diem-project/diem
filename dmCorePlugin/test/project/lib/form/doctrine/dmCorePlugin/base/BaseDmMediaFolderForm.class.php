<?php

/**
 * DmMediaFolder form base class.
 *
 * @method DmMediaFolder getObject() Returns the current form's model object
 *
 * @package    retest
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseDmMediaFolderForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'       => new sfWidgetFormInputHidden(),
      'rel_path' => new sfWidgetFormInputText(),
      'lft'      => new sfWidgetFormInputText(),
      'rgt'      => new sfWidgetFormInputText(),
      'level'    => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'       => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'rel_path' => new sfValidatorString(array('max_length' => 255)),
      'lft'      => new sfValidatorInteger(array('required' => false)),
      'rgt'      => new sfValidatorInteger(array('required' => false)),
      'level'    => new sfValidatorInteger(array('required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'DmMediaFolder', 'column' => array('rel_path')))
    );

    $this->widgetSchema->setNameFormat('dm_media_folder[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'DmMediaFolder';
  }

}
