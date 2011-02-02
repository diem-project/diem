<?php

/**
 * PluginDmUser form.
 *
 * @package    sfDoctrineGuardPlugin
 * @subpackage form
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: PluginDmUserForm.class.php 23536 2009-11-02 21:41:21Z Kris.Wallsmith $
 */
abstract class PluginDmUserForm extends BaseDmUserForm
{
  /**
   * @see sfForm
   */
  public function setup()
  {
    parent::setup();

    unset(
      $this['last_login'],
      $this['created_at'],
      $this['updated_at'],
      $this['salt'],
      $this['algorithm'],
      $this['groups_list'],
      $this['permissions_list'],
      $this['is_active'],
      $this['is_super_admin'],
      $this['forgot_password_code']
    );

    if($this->needsWidget('password'))
    {
	    $this->widgetSchema['password'] = new sfWidgetFormInputPassword(array(), array(
	      'autocomplete' => 'off'
	    ));
	    $this->validatorSchema['password']->setOption('required', $this->object->isNew());
	    $this->widgetSchema['password_again'] = new sfWidgetFormInputPassword(array(
	      'label' => 'Password (again)'
	    ), array(
	      'autocomplete' => 'off'
	    ));
	    $this->validatorSchema['password_again'] = clone $this->validatorSchema['password'];
	
	    $this->widgetSchema->moveField('password_again', 'after', 'password');
	    $this->mergePostValidator(new sfValidatorSchemaCompare('password', sfValidatorSchemaCompare::EQUAL, 'password_again', array(), array('invalid' => 'The two passwords must be the same.')));
    }

    if($this->needsWidget('username'))
    {
	    $this->validatorSchema['username'] = new sfValidatorAnd(array(
	      $this->validatorSchema['username'],
	      new sfValidatorRegex(array('pattern' => '/^[\w\d\-\s@\.]+$/')),
	    ));
    }

    $this->needsWidget('email') && $this->changeToEmail('email');
    
    if ($this->isCaptchaEnabled())
    {
      $this->addCaptcha();
    }
  }

  public function addCaptcha()
  {
    $this->widgetSchema['captcha'] = new sfWidgetFormReCaptcha(array(
      'label'       => 'Captcha',
      'public_key'  => sfConfig::get('app_recaptcha_public_key')
    ));

    $this->validatorSchema['captcha'] = new sfValidatorReCaptcha(array(
      'private_key' => sfConfig::get('app_recaptcha_private_key')
    ));
  }

  public function isCaptchaEnabled()
  {
    return sfConfig::get('app_recaptcha_enabled');
  }
}
