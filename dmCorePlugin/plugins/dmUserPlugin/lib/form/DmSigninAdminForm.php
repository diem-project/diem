<?php

class DmSigninAdminForm extends DmSigninBaseForm
{
  /**
   * @see sfForm
   */
  public function configure()
  {
    $this
    ->changeToHidden('remember')
    ->setDefault('remember', true);
  }
}
