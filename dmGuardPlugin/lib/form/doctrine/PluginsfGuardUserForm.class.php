<?php

/**
 * PluginsfGuardUser form.
 *
 * @package    form
 * @subpackage sfGuardUser
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
abstract class PluginsfGuardUserForm extends BasesfGuardUserForm
{
  public function configure()
  {
    $this->validatorSchema['username'] = new sfValidatorAnd(array(
      $this->validatorSchema['username'],
      new sfValidatorRegex(array('pattern' => '/^[\w\d\-\s@\.]+$/')),
    ));
    
    $this->validatorSchema['email'] = new sfValidatorAnd(array(
      $this->validatorSchema['email'],
      new sfValidatorEmail(),
    ));
  }
}