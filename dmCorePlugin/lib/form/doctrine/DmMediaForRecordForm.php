<?php

class DmMediaForRecordForm extends DmMediaForm
{
  protected
  $record;

  public function configure()
  {
    parent::configure();

    $this->widgetSchema['id']->setAttribute('class', 'dm_media_id');

    $this->getWidgetSchema()
    ->getFormFormatter()
    ->setDecoratorFormat("<ul class=\"dm_media_for_record_form dm_form_elements\">\n  %content%</ul>");
  }

  public static function factory(myDoctrineRecord $record, $local, $alias, $required)
  {
    /*
     * Check first is local column has a value
     * not to modify the record
     */
    if ($record->get($local))
    {
      $media = $record->get($alias);
    }
    else
    {
      $media = new DmMedia;
      $media->set('dm_media_folder_id', $record->getDmMediaFolder()->get('id'));
    }

    $form = new self($media);
    $form->configureRequired($required);
    $form->setRecord($record);
    return $form;
  }
  
  public function checkFolder($validator, $values)
  {
    if (!empty($values['file']))
    {
      $values['dm_media_folder_id'] = $this->record->getDmMediaFolder()->get('id');
    }
    
    return parent::checkFolder($validator, $values);
  }
  
  public function configureRequired($required)
  {
    $this->getValidator('file')->setOption('required', $required && $this->getValidator('file')->getOption('required'));

    /*
     * Add checkbox to remove Media
     */
    if(!$required && $this->object->exists() && !isset($this->widgetSchema['remove']))
    {
      $this->widgetSchema['remove'] = new sfWidgetFormInputCheckbox;
      $this->validatorSchema['remove'] = new sfValidatorBoolean;
    }
    elseif(isset($this->widgetSchema['remove']))
    {
      unset($this->widgetSchema['remove'], $this->validatorSchema['remove']);
    }
  }
  
  protected function setRecord(dmDoctrineRecord $record)
  {
    $this->record = $record;
  }

}