<?php

/**
 * DmZone form base class.
 *
 * @method DmZone getObject() Returns the current form's model object
 *
 * @package    retest
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id$
 * @generator  Diem 5.4.0-DEV
 */
abstract class BaseDmZoneForm extends BaseFormDoctrine
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
		if($this->needsWidget('css_class')){
			$this->setWidget('css_class', new sfWidgetFormInputText());
			$this->setValidator('css_class', new sfValidatorString(array('max_length' => 255, 'required' => false)));
		}
		//column
		if($this->needsWidget('width')){
			$this->setWidget('width', new sfWidgetFormInputText());
			$this->setValidator('width', new sfValidatorString(array('max_length' => 15, 'required' => false)));
		}
		//column
		if($this->needsWidget('position')){
			$this->setWidget('position', new sfWidgetFormInputText());
			$this->setValidator('position', new sfValidatorInteger(array('required' => false)));
		}


		//one to many
		if($this->needsWidget('widgets_list')){
			$this->setWidget('widgets_list', new sfWidgetFormDmPaginatedDoctrineChoice(array('multiple' => true, 'model' => 'DmWidget', 'expanded' => true)));
			$this->setValidator('widgets_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmWidget', 'required' => false)));
		}

		//one to one
		if($this->needsWidget('dm_area_id')){
			$this->setWidget('dm_area_id', new sfWidgetFormDmDoctrineChoice(array('multiple' => false, 'model' => 'DmArea', 'expanded' => false)));
			$this->setValidator('dm_area_id', new sfValidatorDoctrineChoice(array('multiple' => false, 'model' => 'DmArea', 'required' => true)));
		}




    $this->widgetSchema->setNameFormat('dm_zone[%s]');

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
    return 'DmZone';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['widgets_list']))
    {
        $this->setDefault('widgets_list', array_merge((array)$this->getDefault('widgets_list'),$this->object->Widgets->getPrimaryKeys()));
    }

  }

  protected function doSave($con = null)
  {
    $this->saveWidgetsList($con);

    parent::doSave($con);
  }

  public function saveWidgetsList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['widgets_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->Widgets->getPrimaryKeys();
    $values = $this->getValue('widgets_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('Widgets', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('Widgets', array_values($link));
    }
  }

}
