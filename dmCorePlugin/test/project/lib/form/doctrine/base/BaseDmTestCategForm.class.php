<?php

/**
 * DmTestCateg form base class.
 *
 * @method DmTestCateg getObject() Returns the current form's model object
 *
 * @package    retest
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id$
 * @generator  Diem 5.4.0-DEV
 */
abstract class BaseDmTestCategForm extends BaseFormDoctrine
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
		if($this->needsWidget('domains_list')){
			$this->setWidget('domains_list', new sfWidgetFormDmPaginatedDoctrineChoice(array('multiple' => true, 'model' => 'DmTestDomain', 'expanded' => true)));
			$this->setValidator('domains_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmTestDomain', 'required' => false)));
		}

		//one to many
		if($this->needsWidget('dm_test_domain_categ_list')){
			$this->setWidget('dm_test_domain_categ_list', new sfWidgetFormDmPaginatedDoctrineChoice(array('multiple' => true, 'model' => 'DmTestDomainCateg', 'expanded' => true)));
			$this->setValidator('dm_test_domain_categ_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmTestDomainCateg', 'required' => false)));
		}
		//one to many
		if($this->needsWidget('posts_list')){
			$this->setWidget('posts_list', new sfWidgetFormDmPaginatedDoctrineChoice(array('multiple' => true, 'model' => 'DmTestPost', 'expanded' => true)));
			$this->setValidator('posts_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmTestPost', 'required' => false)));
		}





    if('embed' == sfConfig::get('dm_i18n_form'))
    {
      $this->embedI18n(sfConfig::get('dm_i18n_cultures'));
    }
    else
    {
      $this->mergeI18nForm();
    }

    $this->widgetSchema->setNameFormat('dm_test_categ[%s]');

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
    return 'DmTestCateg';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['domains_list']))
    {
        $this->setDefault('domains_list', array_merge((array)$this->getDefault('domains_list'),$this->object->Domains->getPrimaryKeys()));
    }

    if (isset($this->widgetSchema['dm_test_domain_categ_list']))
    {
        $this->setDefault('dm_test_domain_categ_list', array_merge((array)$this->getDefault('dm_test_domain_categ_list'),$this->object->DmTestDomainCateg->getPrimaryKeys()));
    }

    if (isset($this->widgetSchema['posts_list']))
    {
        $this->setDefault('posts_list', array_merge((array)$this->getDefault('posts_list'),$this->object->Posts->getPrimaryKeys()));
    }

    if (isset($this->widgetSchema['translation_list']))
    {
        $this->setDefault('translation_list', array_merge((array)$this->getDefault('translation_list'),$this->object->Translation->getPrimaryKeys()));
    }

  }

  protected function doSave($con = null)
  {
    $this->saveDomainsList($con);
    $this->saveDmTestDomainCategList($con);
    $this->savePostsList($con);
    $this->saveTranslationList($con);

    parent::doSave($con);
  }

  public function saveDomainsList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['domains_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->Domains->getPrimaryKeys();
    $values = $this->getValue('domains_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('Domains', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('Domains', array_values($link));
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

  public function savePostsList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['posts_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->Posts->getPrimaryKeys();
    $values = $this->getValue('posts_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('Posts', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('Posts', array_values($link));
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

}
