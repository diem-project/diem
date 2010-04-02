<?php

class AddMediaByIdForm extends dmForm
{
  protected
  $record;
  
  public function __construct(dmDoctrineRecord $record = null)
  {
    $this->record = $record;

    parent::__construct();
  }
  
  public function configure()
  {
    $this->widgetSchema['media_id'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['media_id'] = new sfValidatorDoctrineChoice(
      array('multiple' => false, 'model' => 'DmMedia')
    );

    $this->widgetSchema['model'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['model'] = new sfValidatorString();

    $this->widgetSchema['pk'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['pk'] = new sfValidatorInteger();

    if($this->record)
    {
      $this->setDefault('model', get_class($this->record));
      $this->setDefault('pk', $this->record->getPrimaryKey());
    }
  }

  public function save()
  {
    $this->getRecord()->addMedia($this->getDmMedia());
  }

  protected function getRecord()
  {
    return dmDb::table($this->getValue('model'))->find($this->getValue('pk'));
  }

  protected function getDmMedia()
  {
    return dmDb::table('DmMedia')->find($this->getValue('media_id'));
  }
}