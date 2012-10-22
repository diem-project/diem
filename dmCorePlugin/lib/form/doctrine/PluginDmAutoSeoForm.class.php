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
  $testRecord,
  $attributes;
  
  public function setup()
  {
    parent::setup();

    $this->useFields($this->getRules());
    
    $this->widgetSchema['description'] = new sfWidgetFormTextarea(array(), array('rows' =>2));
    
    $this->widgetSchema->setHelps(array(
      'slug' => 'The page url, without domain name. Must be unique. If the slug does not start with a \'/\', the parent slug is added.',
      'title' => 'The page title, without prefix nor suffix. Should be unique.',
      'name' => 'The page name, used by links to this page. Should be unique.',
      'h1' => 'Assign first header here or let it blank to let the designer choose it. Should be unique.',
      'description' => 'The page description meta, frequently displayed in search engines result page.',
      'keywords' => 'Provides additional meta informations to the page. Also used by Diem internal search engine.',
    ));

    $this->mergePostValidator(new sfValidatorCallback(array('callback' => array($this, 'checkRules'))));
    
    $this->testRecord = $this->object->getTargetDmModule()->getTable()->findOne();
  }
  
  public function getRules()
  {
    return $this->object->getTargetDmModule()->getTable()->getAutoSeoFields();
  }
  
  public function checkRules($validator, $values)
  {
    if($this->testRecord)
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
    }

    return $values;
  }
  
  protected function validateRule($key, $value)
  {
    if (!$this->seoSynchronizer)
    {
      throw new dmException('You must provide a dmSeoSynchronizer instance');
    }
    
    return $this->seoSynchronizer->validatePattern($this->object->getTargetDmModule(), $key, $value, $this->testRecord);
  }
  
  public function setSeoSynchronizer(dmSeoSynchronizer $seoSynchronizer)
  {
    $this->seoSynchronizer = $seoSynchronizer;
  }
  
  public function render($attributes = array())
  {
  	return $this->getFormFieldSchema()->render($this->attributes);
  }
}