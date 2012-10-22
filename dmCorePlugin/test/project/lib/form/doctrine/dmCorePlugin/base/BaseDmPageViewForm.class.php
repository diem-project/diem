<?php

/**
 * DmPageView form base class.
 *
 * @method DmPageView getObject() Returns the current form's model object
 *
 * @package    retest
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id$
 * @generator  Diem 5.4.0-DEV
 */
abstract class BaseDmPageViewForm extends BaseFormDoctrine
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


		//one to many
		if($this->needsWidget('areas_list')){
			$this->setWidget('areas_list', new sfWidgetFormDmPaginatedDoctrineChoice(array('multiple' => true, 'model' => 'DmArea', 'expanded' => true)));
			$this->setValidator('areas_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmArea', 'required' => false)));
		}

		//one to one
		if($this->needsWidget('dm_layout_id')){
			$this->setWidget('dm_layout_id', new sfWidgetFormDmDoctrineChoice(array('multiple' => false, 'model' => 'DmLayout', 'expanded' => false)));
			$this->setValidator('dm_layout_id', new sfValidatorDoctrineChoice(array('multiple' => false, 'model' => 'DmLayout', 'required' => false)));
		}




    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'DmPageView', 'column' => array('module', 'action')))
    );

    $this->widgetSchema->setNameFormat('dm_page_view[%s]');

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
    return 'DmPageView';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['areas_list']))
    {
        $this->setDefault('areas_list', array_merge((array)$this->getDefault('areas_list'),$this->object->Areas->getPrimaryKeys()));
    }

  }

  protected function doSave($con = null)
  {
    $this->saveAreasList($con);

    parent::doSave($con);
  }

  public function saveAreasList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['areas_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->Areas->getPrimaryKeys();
    $values = $this->getValue('areas_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('Areas', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('Areas', array_values($link));
    }
  }

}
