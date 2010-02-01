<?php

/**
 * PluginDmAutoSeo form.
 *
 * @package    form
 * @subpackage DmAutoSeo
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
abstract class PluginDmAutoSeoForm extends BaseDmAutoSeoForm
{
  protected
  $seoSynchronizer,
  $testRecord;
  
  public function setup()
  {
    parent::setup();
    
    $this->useFields($this->getRules());
    
    $this->widgetSchema['description'] = new sfWidgetFormTextarea(array(), array('rows' =>2));
    
    $this->widgetSchema->setHelps(array(
      'slug' => 'The page url, without domain name. Must be unique. If the slug does not start with a /, the parent\'s page slug will be added.',
      'title' => 'The page title, without '.self::$serviceContainer->getService('helper')->link('@dm_config_panel')->anchor('dm_setting_group_seo')->text('prefix & suffix').'. Should be unique.',
      'name' => 'The page name is used by links to this page. Should be unique.',
      'h1' => 'Assign first header here or let it blank to let the webdesigner choose it. Should be unique.',
      'description' => 'The page description meta, frequently displayed in search engines result page.',
      'keywords' => 'Never used by search-engine. Provide meta informations to the page.'
    ));

    $this->mergePostValidator(new sfValidatorCallback(array('callback' => array($this, 'checkRules'))));
  }
  
  public function getRules()
  {
    $rules = DmPage::getAutoSeoFields();
    
    if (!sfConfig::get('dm_seo_use_keywords'))
    {
      unset($rules[array_search('keywords', $rules)]);
    }
    
    return $rules;
  }
  
  public function checkRules($validator, $values)
  {
    foreach($this->getRules() as $rule)
    {
      if (!$this->validateRule($rule, $values[$rule]))
      {
        $error = new sfValidatorError($validator, 'This rule is not valid');
    
        // throw an error bound to the password field
        throw new sfValidatorErrorSchema($validator, array($rule => $error));
      }
    }

    return $values;
  }
  
  protected function validateRule($key, $value)
  {
    if (!$this->seoSynchronizer)
    {
      throw new dmException('You must provide a dmSeoSynchronizer instance');
    }
    
    return $this->seoSynchronizer->validatePattern($this->object->getTargetDmModule(), $key, $value, $this->getTestRecord());
  }
  
  public function getTestRecord()
  {
    if (null === $this->testRecord)
    {
      $this->testRecord = $this->object->getTargetDmModule()->getTable()->findOne();
    }
    
    return $this->testRecord;
  }
  
  public function setSeoSynchronizer(dmSeoSynchronizer $seoSynchronizer)
  {
    $this->seoSynchronizer = $seoSynchronizer;
  }
}