<?php

class dmConfigForm extends dmForm
{

  public function configure()
  {
  }

  public function save()
  {
    foreach($this->widgetSchema->getFields() as $name => $field)
    {
      dmConfig::set($name, $this->getValue($name));
    }
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

//    $this->widgetSchema[$settingName]->setDefault($setting->get('value'));

    $this->widgetSchema->setHelp($settingName, $setting->get('description'));

    $this->validatorSchema[$settingName] = $this->getSettingValidator($setting)->setOption('required', false);
  }

  public function removeSetting($settingName)
  {
    unset($this[$settingName]);
  }

  public function getSettingWidget(DmSetting $setting)
  {
    $method = 'get'.dmString::camelize($setting->type).'SettingWidget';

    return $this->$method($setting);
  }

  public function getSettingValidator(DmSetting $setting)
  {
    $method = 'get'.dmString::camelize($setting->type).'SettingValidator';

    return $this->$method($setting);
  }

  // Type Text
  protected function getTextSettingWidget(DmSetting $setting)
  {
    $widget = new sfWidgetFormInputText(array(), $setting->getParamsArray());

    return $widget->setDefault($setting->get('value'));
  }

  protected function getTextSettingValidator(DmSetting $setting)
  {
    return new sfValidatorString();
  }

  // Type Textarea
  protected function getTextareaSettingWidget(DmSetting $setting)
  {
    $widget = new sfWidgetFormTextarea(array(), $setting->getParamsArray());

    return $widget->setDefault($setting->get('value'));
  }

  protected function getTextareaSettingValidator(DmSetting $setting)
  {
    return new sfValidatorString();
  }

  // Type Number
  protected function getNumberSettingWidget(DmSetting $setting)
  {
    $widget = new sfWidgetFormInputText(array(), $setting->getParamsArray());

    return $widget->setDefault($setting->get('value'));
  }

  protected function getNumberSettingValidator(DmSetting $setting)
  {
    return new sfValidatorNumber();
  }

  // Type Boolean
  protected function getBooleanSettingWidget(DmSetting $setting)
  {
    $widget = new sfWidgetFormInputCheckbox(array(), $setting->getParamsArray());

    return $widget->setDefault(1 == $setting->get('value') ? true : false);
  }

  protected function getBooleanSettingValidator(DmSetting $setting)
  {
    return new sfValidatorBoolean();
  }

  // Type Datetime
  protected function getDatetimeSettingWidget(DmSetting $setting)
  {
    $widget = new sfWidgetFormDateTime(array(), $setting->getParamsArray());

    return $widget->setDefault($setting->get('value'));
  }

  protected function getDatetimeSettingValidator(DmSetting $setting)
  {
    return new sfValidatorDateTime();
  }

  // Type Date
  protected function getDateSettingWidget(DmSetting $setting)
  {
    $widget = new sfWidgetFormDmDate(array(), $setting->getParamsArray());

    return $widget->setDefault($setting->get('value'));
  }

  protected function getDateSettingValidator(DmSetting $setting)
  {
    return new dmValidatorDate();
  }

  // Type Time
  protected function getTimeSettingWidget(DmSetting $setting)
  {
    $widget = new sfWidgetFormDateTime(array('format' => '%time%'), $setting->getParamsArray());

    return $widget->setDefault($setting->get('value'));
  }

  protected function getTimeSettingValidator(DmSetting $setting)
  {
    return new sfValidatorDateTime(array('datetime_output' => 'H:i'));
  }

  // Type Select List
  protected function getSelectSettingWidget(DmSetting $setting)
  {
    $widget = new sfWidgetFormSelect(array('choices' => $setting->getParamsArray()));

    return $widget->setDefault($setting->get('value'));
  }

  protected function getSelectSettingValidator(DmSetting $setting)
  {
    return new sfValidatorChoice(array('choices' => array_keys($setting->getParamsArray())));
  }

}