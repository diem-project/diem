<?php

/**
 * DmTestDomain form base class.
 *
 * @method DmTestDomain getObject() Returns the current form's model object
 *
 * @package    retest
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id$
 * @generator  Diem 5.4.0-DEV
 */
abstract class BaseDmTestDomainForm extends BaseFormDoctrine
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
		if($this->needsWidget('created_at')){
			$this->setWidget('created_at', new sfWidgetFormDateTime());
			$this->setValidator('created_at', new sfValidatorDateTime());
		}
		//column
		if($this->needsWidget('updated_at')){
			$this->setWidget('updated_at', new sfWidgetFormDateTime());
			$this->setValidator('updated_at', new sfValidatorDateTime());
		}
		//column
		if($this->needsWidget('position')){
			$this->setWidget('position', new sfWidgetFormInputText());
			$this->setValidator('position', new sfValidatorInteger(array('required' => false)));
		}

		//many to many
		if($this->needsWidget('categs_list')){
			$this->setWidget('categs_list', new sfWidgetFormDmPaginatedDoctrineChoice(array('multiple' => true, 'model' => 'DmTestCateg', 'expanded' => true)));
			$this->setValidator('categs_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmTestCateg', 'required' => false)));
		}
		//many to many
		if($this->needsWidget('tags_list')){
			$this->setWidget('tags_list', new sfWidgetFormDmPaginatedDoctrineChoice(array('multiple' => true, 'model' => 'DmTag', 'expanded' => true)));
			$this->setValidator('tags_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmTag', 'required' => false)));
		}

		//one to many
		if($this->needsWidget('dm_test_domain_categ_list')){
			$this->setWidget('dm_test_domain_categ_list', new sfWidgetFormDmPaginatedDoctrineChoice(array('multiple' => true, 'model' => 'DmTestDomainCateg', 'expanded' => true)));
			$this->setValidator('dm_test_domain_categ_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmTestDomainCateg', 'required' => false)));
		}
		//one to many
		if($this->needsWidget('dm_test_domain_dm_tag_list')){
			$this->setWidget('dm_test_domain_dm_tag_list', new sfWidgetFormDmPaginatedDoctrineChoice(array('multiple' => true, 'model' => 'DmTestDomainDmTag', 'expanded' => true)));
			$this->setValidator('dm_test_domain_dm_tag_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmTestDomainDmTag', 'required' => false)));
		}





    if('embed' == sfConfig::get('dm_i18n_form'))
    {
      $this->embedI18n(sfConfig::get('dm_i18n_cultures'));
    }
    else
    {
      $this->mergeI18nForm();
    }

    $this->widgetSchema->setNameFormat('dm_test_domain[%s]');

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
    return 'DmTestDomain';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['categs_list']))
    {
        $this->setDefault('categs_list', array_merge((array)$this->getDefault('categs_list'),$this->object->Categs->getPrimaryKeys()));
    }

    if (isset($this->widgetSchema['tags_list']))
    {
        $this->setDefault('tags_list', array_merge((array)$this->getDefault('tags_list'),$this->object->Tags->getPrimaryKeys()));
    }

    if (isset($this->widgetSchema['dm_test_domain_categ_list']))
    {
        $this->setDefault('dm_test_domain_categ_list', array_merge((array)$this->getDefault('dm_test_domain_categ_list'),$this->object->DmTestDomainCateg->getPrimaryKeys()));
    }

    if (isset($this->widgetSchema['translation_list']))
    {
        $this->setDefault('translation_list', array_merge((array)$this->getDefault('translation_list'),$this->object->Translation->getPrimaryKeys()));
    }

    if (isset($this->widgetSchema['dm_test_domain_dm_tag_list']))
    {
        $this->setDefault('dm_test_domain_dm_tag_list', array_merge((array)$this->getDefault('dm_test_domain_dm_tag_list'),$this->object->DmTestDomainDmTag->getPrimaryKeys()));
    }

  }

  protected function doSave($con = null)
  {
    $this->saveCategsList($con);
    $this->saveTagsList($con);
    $this->saveDmTestDomainCategList($con);
    $this->saveTranslationList($con);
    $this->saveDmTestDomainDmTagList($con);

    parent::doSave($con);
  }

  public function saveCategsList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['categs_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->Categs->getPrimaryKeys();
    $values = $this->getValue('categs_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('Categs', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('Categs', array_values($link));
    }
  }

  public function saveTagsList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['tags_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->Tags->getPrimaryKeys();
    $values = $this->getValue('tags_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('Tags', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('Tags', array_values($link));
    }
  }

  public function saveDmTestDomainCategList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['dm_test_domain_categ_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->DmTestDomainCateg->getPrimaryKeys();
    $values = $this->getValue('dm_test_domain_categ_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('DmTestDomainCateg', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('DmTestDomainCateg', array_values($link));
    }
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

  public function saveDmTestDomainDmTagList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['dm_test_domain_dm_tag_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->DmTestDomainDmTag->getPrimaryKeys();
    $values = $this->getValue('dm_test_domain_dm_tag_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('DmTestDomainDmTag', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('DmTestDomainDmTag', array_values($link));
    }
  }

}
