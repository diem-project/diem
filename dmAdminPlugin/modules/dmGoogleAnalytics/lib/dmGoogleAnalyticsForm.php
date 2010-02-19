<?php

class dmGoogleAnalyticsForm extends dmForm
{
  protected
  $gapi;

  public function setGapi(dmGapi $gapi)
  {
    $this->gapi = $gapi;
  }

  public function configure()
  {
    $this->widgetSchema['key'] = new sfWidgetFormInputText();
    $this->validatorSchema['key'] = new sfValidatorString(array('required' => false));
    $this->widgetSchema->setHelp('key', dmDb::table('DmSetting')->findOneByName('ga_key')->description);
    $this->setDefault('key', dmConfig::get('ga_key'));
    
    $this->widgetSchema['email'] = new sfWidgetFormInputText();
    $this->validatorSchema['email'] = new sfValidatorEmail(array('required' => false));
    $this->widgetSchema->setHelp('email', 'Required to display google analytics data into Diem');

    $this->widgetSchema['password'] = new sfWidgetFormInputPassword();
    $this->validatorSchema['password'] = new sfValidatorString(array('required' => false));
    $this->widgetSchema->setHelp('password', 'Required to display google analytics data into Diem');

    $this->mergePostValidator(new sfValidatorCallback(array('callback' => array($this, 'tokenize'))));
  }

  public function tokenize($validator, $values)
  {
    $values['token'] = null;
    
    if($values['email'] || $values['password'])
    {
      try
      {
        $this->gapi->authenticate($values['email'], $values['password']);

        // save token
        $values['token'] = $this->gapi->getAuthToken();
      }
      catch(dmGapiException $e)
      {
        // probably bad email/password
        // throw an error bound to the password field
        throw new sfValidatorErrorSchema($validator, array('email' => new sfValidatorError($validator, 'Bad email or password')));
      }
    }
    
    return $values;
  }

  public function save()
  {
    if($this->getValue('token'))
    {
      dmConfig::set('ga_token', $this->getValue('token'));
    }
  }
}