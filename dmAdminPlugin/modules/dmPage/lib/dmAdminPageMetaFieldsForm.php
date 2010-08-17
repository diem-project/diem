<?php

class dmAdminPageMetaFieldsForm extends dmForm
{
  protected
  $metaView;

  /**
   * @see sfForm
   */
  public function __construct(dmAdminPageMetaView $metaView, $options = array(), $CSRFSecret = null)
  {
    $this->metaView = $metaView;
    
    return parent::__construct(array(), $options, $CSRFSecret);
  }

  public function configure()
  {
    $this->widgetSchema['fields'] = new sfWidgetFormSelectCheckbox(array(
      'choices' => $this->getTranslatedChoicesArray()
    ));

    $this->validatorSchema['fields'] = new sfValidatorChoice(array(
      'choices' => $this->metaView->getAvailableFields(),
      'multiple' => true
    ));

    $this->setDefault('fields', array('lft', 'name', 'slug', 'title', 'is_active'));
  }

  protected function getTranslatedChoicesArray()
  {
    $choices = array();
    
    foreach($this->metaView->getAvailableFields() as $field)
    {
      $choices[$field] = $this->metaView->renderField($field);
    }

    return $choices;
  }
}