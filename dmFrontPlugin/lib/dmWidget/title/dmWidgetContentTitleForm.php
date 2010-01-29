<?php

class dmWidgetContentTitleForm extends dmWidgetPluginForm
{

  public function configure()
  {
    $this->widgetSchema['text'] = new sfWidgetFormTextarea(array(), array(
      'rows' => 2
    ));
    $this->widgetSchema['tag']  = new sfWidgetFormChoice(array('choices' => $this->getTagNames()));

    $this->validatorSchema['text'] = new sfValidatorString(array('required' => true));
    $this->validatorSchema['tag']  = new sfValidatorChoice(array('choices' => $this->getTagNames(), 'required' => true));

    parent::configure();
  }

  protected function getTagNames()
  {
    return dmArray::valueToKey(array('h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'div'));
  }
}