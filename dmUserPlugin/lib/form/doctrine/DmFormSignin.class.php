<?php

/**
 * DmFormSignin for DmAuth signin action
 *
 * @package    sfDoctrineGuardPlugin
 * @subpackage form
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: DmFormSignin.class.php 23536 2009-11-02 21:41:21Z Kris.Wallsmith $
 */
class DmFormSignin extends BaseDmFormSignin
{
  /**
   * @see sfForm
   */
  public function configure()
  {
    $this->validatorSchema->setPostValidator(new dmValidatorUser());

    $this->widgetSchema->setFormFormatterName('dmList');

    $this->widgetSchema['remember'] = new sfWidgetFormInputHidden();

    $this->setDefault('remember', 1);
  }
}
