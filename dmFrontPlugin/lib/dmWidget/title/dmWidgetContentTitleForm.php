<?php

class dmWidgetContentTitleForm extends dmWidgetPluginForm
{

  protected static
  $tags = array('h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'div');

  public function getTags()
  {
    return dmArray::valueToKey(self::$tags);
  }

  public function configure()
  {
    $this->widgetSchema['text'] = new sfWidgetFormTextarea(array(), array(
      'rows' => 2
    ));
    $this->widgetSchema['tag']  = new sfWidgetFormChoice(array('choices' => self::getTags()));

    $this->validatorSchema['text'] = new sfValidatorString(array('required' => true));
    $this->validatorSchema['tag']  = new sfValidatorChoice(array('choices' => self::getTags(), 'required' => true));

    parent::configure();
  }

}