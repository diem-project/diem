<?php

/**
 * DmTestFruit form base class.
 *
 * @method DmTestFruit getObject() Returns the current form's model object
 *
 * @package    retest
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id$
 * @generator  Diem 5.4.0-DEV
 */
abstract class BaseDmTestFruitForm extends BaseFormDoctrine
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
		if($this->needsWidget('title')){
			$this->setWidget('title', new sfWidgetFormInputText());
			$this->setValidator('title', new sfValidatorString(array('max_length' => 255, 'required' => false)));
		}

		//many to many
		if($this->needsWidget('tags_list')){
			$this->setWidget('tags_list', new sfWidgetFormDmPaginatedDoctrineChoice(array('multiple' => true, 'model' => 'DmTag', 'expanded' => true)));
			$this->setValidator('tags_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmTag', 'required' => false)));
		}

		//one to many
		if($this->needsWidget('dm_test_fruit_dm_tag_list')){
			$this->setWidget('dm_test_fruit_dm_tag_list', new sfWidgetFormDmPaginatedDoctrineChoice(array('multiple' => true, 'model' => 'DmTestFruitDmTag', 'expanded' => true)));
			$this->setValidator('dm_test_fruit_dm_tag_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmTestFruitDmTag', 'required' => false)));
		}

		//one to one
		if($this->needsWidget('created_by')){
			$this->setWidget('created_by', new sfWidgetFormDmDoctrineChoice(array('multiple' => false, 'model' => 'DmUser', 'expanded' => false)));
			$this->setValidator('created_by', new sfValidatorDoctrineChoice(array('multiple' => false, 'model' => 'DmUser', 'required' => false)));
		}
		//one to one
		if($this->needsWidget('updated_by')){
			$this->setWidget('updated_by', new sfWidgetFormDmDoctrineChoice(array('multiple' => false, 'model' => 'DmUser', 'expanded' => false)));
			$this->setValidator('updated_by', new sfValidatorDoctrineChoice(array('multiple' => false, 'model' => 'DmUser', 'required' => false)));
		}




    $this->widgetSchema->setNameFormat('dm_test_fruit[%s]');

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
    return 'DmTestFruit';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['tags_list']))
    {
        $this->setDefault('tags_list', array_merge((array)$this->getDefault('tags_list'),$this->object->Tags->getPrimaryKeys()));
    }

    if (isset($this->widgetSchema['dm_test_fruit_dm_tag_list']))
    {
        $this->setDefault('dm_test_fruit_dm_tag_list', array_merge((array)$this->getDefault('dm_test_fruit_dm_tag_list'),$this->object->DmTestFruitDmTag->getPrimaryKeys()));
    }

  }

  protected function doSave($con = null)
  {
    $this->saveTagsList($con);
    $this->saveDmTestFruitDmTagList($con);

    parent::doSave($con);
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

}
