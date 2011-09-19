<?php

/**
 * DmTestPostTranslation form base class.
 *
 * @method DmTestPostTranslation getObject() Returns the current form's model object
 *
 * @package    retest
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id$
 * @generator  Diem 5.4.0-DEV
 */
abstract class BaseDmTestPostTranslationForm extends BaseFormDoctrine
{
  public function setup()
  {
    parent::setup();

		//column
		if($this->needsWidget('title')){
			$this->setWidget('title', new sfWidgetFormInputText());
			$this->setValidator('title', new sfValidatorString(array('max_length' => 255)));
		}
		//column
		if($this->needsWidget('excerpt')){
			$this->setWidget('excerpt', new sfWidgetFormTextarea());
			$this->setValidator('excerpt', new sfValidatorString(array('max_length' => 800, 'required' => false)));
		}
		//column
		if($this->needsWidget('body')){
			$this->setWidget('body', new sfWidgetFormTextarea());
			$this->setValidator('body', new sfValidatorString(array('required' => false)));
		}
		//column
		if($this->needsWidget('url')){
			$this->setWidget('url', new sfWidgetFormInputText());
			$this->setValidator('url', new dmValidatorLinkUrl(array('max_length' => 255, 'required' => false)));
		}
		//column
		if($this->needsWidget('is_active')){
			$this->setWidget('is_active', new sfWidgetFormInputCheckbox());
			$this->setValidator('is_active', new sfValidatorBoolean(array('required' => false)));
		}
		//column
		if($this->needsWidget('lang')){
			$this->setWidget('lang', new sfWidgetFormInputHidden());
			$this->setValidator('lang', new sfValidatorChoice(array('choices' => array($this->getObject()->get('lang')), 'empty_value' => $this->getObject()->get('lang'), 'required' => false)));
		}
		//column
		if($this->needsWidget('version')){
			$this->setWidget('version', new sfWidgetFormInputText());
			$this->setValidator('version', new sfValidatorInteger(array('required' => false)));
		}


		//one to many
		if($this->needsWidget('version_list')){
			$this->setWidget('version_list', new sfWidgetFormDmPaginatedDoctrineChoice(array('multiple' => true, 'model' => 'DmTestPostTranslationVersion', 'expanded' => true)));
			$this->setValidator('version_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmTestPostTranslationVersion', 'required' => false)));
		}

		//one to one
		if($this->needsWidget('id')){
			$this->setWidget('id', new sfWidgetFormDmDoctrineChoice(array('multiple' => false, 'model' => 'DmTestPost', 'expanded' => false)));
			$this->setValidator('id', new sfValidatorDoctrineChoice(array('multiple' => false, 'model' => 'DmTestPost', 'required' => false)));
		}




    $this->widgetSchema->setNameFormat('dm_test_post_translation[%s]');

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
    return 'DmTestPostTranslation';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['version_list']))
    {
        $this->setDefault('version_list', array_merge((array)$this->getDefault('version_list'),$this->object->Version->getPrimaryKeys()));
    }

  }

  protected function doSave($con = null)
  {
    $this->saveVersionList($con);

    parent::doSave($con);
  }

  public function saveVersionList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['version_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->Version->getPrimaryKeys();
    $values = $this->getValue('version_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('Version', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('Version', array_values($link));
    }
  }

}
