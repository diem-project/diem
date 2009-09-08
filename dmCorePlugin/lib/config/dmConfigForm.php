<?php

class dmConfigForm extends dmForm
{

  public function configure()
  {
  }
  
  public function addSettings(array $settings)
  {
    foreach($settings as $setting)
    {
      $this->addSetting($setting);
    }
  }
  
  public function addSetting(DmSetting $setting)
  {
    $settingName = $setting->get('name');
    
    $this->widgetSchema[$settingName] = $this->getSettingWidget($setting);
    
    $this->widgetSchema[$settingName]->setDefault($setting->get('value'));
    
    $this->widgetSchema->setHelp($settingName, $setting->get('description'));
    
    $this->validatorSchema[$settingName] = $this->getSettingValidator($setting);
  }
  
  public function removeSetting($settingName)
  {
    unset($this[$settingName]);
  }

  protected function getSettingWidget(DmSetting $setting)
  {
    $method = 'get'.dmString::camelize($setting->type).'SettingWidget';
    
    return $this->$method($setting);
  }

  protected function getSettingValidator(DmSetting $setting)
  {
    $method = 'get'.dmString::camelize($setting->type).'SettingValidator';
    
    return $this->$method($setting);
  }

  //Type Textarea
  protected function getTextSettingWidget(DmSetting $setting)
  {
    return new sfWidgetFormInputText(array(), $setting->getOptionsArray());
  }
  protected function getTextSettingValidator(DmSetting $setting)
  {
    return new sfValidatorString();
  }

  //Type Textarea
  protected function getTextareaSettingWidget(DmSetting $setting)
  {
    return new sfWidgetFormTextarea(array(), $setting->getOptionsArray());
  }
  protected function getTextareaSettingValidator(DmSetting $setting)
  {
    return new sfValidatorString();
  }

  // Type Checkbox
  protected function getCheckboxSettingWidget(DmSetting $setting)
  {
    return new sfWidgetFormInputCheckbox(array(), $setting->getOptionsArray());
  }
  protected function getCheckboxSettingValidator(DmSetting $setting)
  {
    return new sfValidatorChoice(array('choices' => array_keys($setting->getOptionsArray())));
  }

  // Type Boolean
  protected function getBooleanSettingWidget(DmSetting $setting)
  {
    return new sfWidgetFormSelectRadio(array('choices' => array(1 => 'Yes', 0 => 'No')), $setting->getOptionsArray());
  }
  protected function getBooleanSettingValidator(DmSetting $setting)
  {
    return new sfValidatorChoice(array('choices' => array(1, 0)));
  }

  //Type Select List
  protected function getSelectSettingWidget(DmSetting $setting)
  {
    return new sfWidgetFormSelect(array('choices' => $setting->getOptionsArray()));
  }
  protected function getSelectSettingValidator(DmSetting $setting)
  {
    return new sfValidatorChoice(array('choices' => array_keys($setting->getOptionsArray())));
  }

  //Type Model
  protected function getModelSettingWidget(DmSetting $setting)
  {
    return new sfWidgetFormInputText(array(), $setting->getOptionsArray());
  }
  protected function getModelSettingValidator(DmSetting $setting)
  {
    return new sfValidatorString();
  }
}