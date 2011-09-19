<?php

/**
 * DmAutoSeo form base class.
 *
 * @method DmAutoSeo getObject() Returns the current form's model object
 *
 * @package    retest
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id$
 * @generator  Diem 5.4.0-DEV
 */
abstract class BaseDmAutoSeoForm extends BaseFormDoctrine
{
  public function setup()
  {
    parent::setup();

		//column
		if($this->needsWidget('id')){
			$this->setWidget('id', new sfWidgetFormInputHidden());
			$this->setValidator('id', new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)));
		}
		//column
		if($this->needsWidget('module')){
			$this->setWidget('module', new sfWidgetFormInputText());
			$this->setValidator('module', new sfValidatorString(array('max_length' => 127)));
		}
		//column
		if($this->needsWidget('action')){
			$this->setWidget('action', new sfWidgetFormInputText());
			$this->setValidator('action', new sfValidatorString(array('max_length' => 127)));
		}
		//column
		if($this->needsWidget('created_at')){
			$this->setWidget('created_at', new sfWidgetFormDateTime());
			$this->setValidator('created_at', new sfValidatorDateTime());
		}
		//column
		if($this->needsWidget('updated_at')){
			$this->setWidget('updated_at', new sfWidgetFormDateTime());
			$this->setValidator('updated_at', new sfValidatorDateTime());
		}







    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'DmAutoSeo', 'column' => array('module', 'action')))
    );

    if('embed' == sfConfig::get('dm_i18n_form'))
    {
      $this->embedI18n(sfConfig::get('dm_i18n_cultures'));
    }
    else
    {
      $this->mergeI18nForm();
    }

    $this->widgetSchema->setNameFormat('dm_auto_seo[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
    
    // Unset automatic fields like 'created_at', 'updated_at', 'position'
    // override this method in your form to keep them
    parent::unsetAutoFields();
  }


  protected function doBind(array $values)
  {
    parent::doBind($values);
  }
  
  public function processValues($values)
  {
    $values = parent::processValues($values);
    return $values;
  }
  
  protected function doUpdateObject($values)
  {
    parent::doUpdateObject($values);
  }

  public function getModelName()
  {
    return 'DmAutoSeo';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['translation_list']))
    {
        $this->setDefault('translation_list', array_merge((array)$this->getDefault('translation_list'),$this->object->Translation->getPrimaryKeys()));
    }

  }

  protected function doSave($con = null)
  {
    $this->saveTranslationList($con);

    parent::doSave($con);
  }

  public function saveTranslationList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['translation_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->Translation->getPrimaryKeys();
    $values = $this->getValue('translation_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('Translation', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('Translation', array_values($link));
    }
  }

}
