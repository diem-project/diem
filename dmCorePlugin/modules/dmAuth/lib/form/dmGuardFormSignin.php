<?php

class dmGuardFormSignin extends sfGuardFormSignin
{
  public function configure()
  {
  	parent::configure();

    $this->validatorSchema->setPostValidator(new dmGuardValidatorUser());

    $this->widgetSchema->setFormFormatterName('dmList');

    $this->widgetSchema['remember'] = new sfWidgetFormInputHidden();

    $this->setDefault('remember', 1);
  }
}
