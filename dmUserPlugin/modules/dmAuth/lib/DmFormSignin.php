<?php

/**
 * DmFormSignin for DmAuth signin action
 *
 * @package    sfDoctrineGuardPlugin
 * @subpackage form
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: DmFormSignin.class.php 23536 2009-11-02 21:41:21Z Kris.Wallsmith $
 */
class DmFormSignin extends BaseForm
{
  /**
   * @see sfForm
   */
  public function configure()
  {
    $this->setWidgets(array(
      'username' => new sfWidgetFormInputText(),
      'password' => new sfWidgetFormInputPassword(array('type' => 'password')),
      'remember' => new sfWidgetFormInputHidden(),
    ));

    $this->setValidators(array(
      'username' => new sfValidatorString(),
      'password' => new sfValidatorString(),
      'remember' => new sfValidatorBoolean(),
    ));

    $this->widgetSchema->setNameFormat('signin[%s]');

    $this->setDefault('remember', true);
    
    $this->validatorSchema->setPostValidator(new dmValidatorUser());

    $this->widgetSchema->setFormFormatterName('dmList');
  }
}
