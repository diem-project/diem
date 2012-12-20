<?php

/**
 * DmTag form base class.
 *
 * @method DmTag getObject() Returns the current form's model object
 *
 * @package    retest
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id$
 * @generator  Diem 5.4.0-DEV
 */
abstract class BaseDmTagForm extends BaseFormDoctrine
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
		if($this->needsWidget('name')){
			$this->setWidget('name', new sfWidgetFormInputText());
			$this->setValidator('name', new sfValidatorString(array('max_length' => 255)));
		}

		//many to many
		if($this->needsWidget('dm_test_fruits_list')){
			$this->setWidget('dm_test_fruits_list', new sfWidgetFormDmPaginatedDoctrineChoice(array('multiple' => true, 'model' => 'DmTestFruit', 'expanded' => true)));
			$this->setValidator('dm_test_fruits_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmTestFruit', 'required' => false)));
		}
		//many to many
		if($this->needsWidget('dm_test_domains_list')){
			$this->setWidget('dm_test_domains_list', new sfWidgetFormDmPaginatedDoctrineChoice(array('multiple' => true, 'model' => 'DmTestDomain', 'expanded' => true)));
			$this->setValidator('dm_test_domains_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmTestDomain', 'required' => false)));
		}

		//one to many
		if($this->needsWidget('dm_test_fruit_dm_tag_list')){
			$this->setWidget('dm_test_fruit_dm_tag_list', new sfWidgetFormDmPaginatedDoctrineChoice(array('multiple' => true, 'model' => 'DmTestFruitDmTag', 'expanded' => true)));
			$this->setValidator('dm_test_fruit_dm_tag_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmTestFruitDmTag', 'required' => false)));
		}
		//one to many
		if($this->needsWidget('dm_test_domain_dm_tag_list')){
			$this->setWidget('dm_test_domain_dm_tag_list', new sfWidgetFormDmPaginatedDoctrineChoice(array('multiple' => true, 'model' => 'DmTestDomainDmTag', 'expanded' => true)));
			$this->setValidator('dm_test_domain_dm_tag_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmTestDomainDmTag', 'required' => false)));
		}





    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'DmTag', 'column' => array('name')))
    );

    $this->widgetSchema->setNameFormat('dm_tag[%s]');

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
    return 'DmTag';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['dm_test_fruits_list']))
    {
        $this->setDefault('dm_test_fruits_list', array_merge((array)$this->getDefault('dm_test_fruits_list'),$this->object->DmTestFruits->getPrimaryKeys()));
    }

    if (isset($this->widgetSchema['dm_test_domains_list']))
    {
        $this->setDefault('dm_test_domains_list', array_merge((array)$this->getDefault('dm_test_domains_list'),$this->object->DmTestDomains->getPrimaryKeys()));
    }

    if (isset($this->widgetSchema['dm_test_fruit_dm_tag_list']))
    {
        $this->setDefault('dm_test_fruit_dm_tag_list', array_merge((array)$this->getDefault('dm_test_fruit_dm_tag_list'),$this->object->DmTestFruitDmTag->getPrimaryKeys()));
    }

    if (isset($this->widgetSchema['dm_test_domain_dm_tag_list']))
    {
        $this->setDefault('dm_test_domain_dm_tag_list', array_merge((array)$this->getDefault('dm_test_domain_dm_tag_list'),$this->object->DmTestDomainDmTag->getPrimaryKeys()));
    }

  }

  protected function doSave($con = null)
  {
    $this->saveDmTestFruitsList($con);
    $this->saveDmTestDomainsList($con);
    $this->saveDmTestFruitDmTagList($con);
    $this->saveDmTestDomainDmTagList($con);

    parent::doSave($con);
  }

  public function saveDmTestFruitsList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['dm_test_fruits_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->DmTestFruits->getPrimaryKeys();
    $values = $this->getValue('dm_test_fruits_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('DmTestFruits', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('DmTestFruits', array_values($link));
    }
  }

  public function saveDmTestDomainsList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['dm_test_domains_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->DmTestDomains->getPrimaryKeys();
    $values = $this->getValue('dm_test_domains_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('DmTestDomains', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('DmTestDomains', array_values($link));
    }
  }

  public function saveDmTestFruitDmTagList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['dm_test_fruit_dm_tag_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->DmTestFruitDmTag->getPrimaryKeys();
    $values = $this->getValue('dm_test_fruit_dm_tag_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('DmTestFruitDmTag', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('DmTestFruitDmTag', array_values($link));
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
