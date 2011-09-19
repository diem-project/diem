<?php

/**
 * DmLayout form base class.
 *
 * @method DmLayout getObject() Returns the current form's model object
 *
 * @package    retest
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id$
 * @generator  Diem 5.4.0-DEV
 */
abstract class BaseDmLayoutForm extends BaseFormDoctrine
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
		//column
		if($this->needsWidget('template')){
			$this->setWidget('template', new sfWidgetFormInputText());
			$this->setValidator('template', new sfValidatorString(array('max_length' => 255, 'required' => false)));
		}
		//column
		if($this->needsWidget('css_class')){
			$this->setWidget('css_class', new sfWidgetFormInputText());
			$this->setValidator('css_class', new sfValidatorString(array('max_length' => 255, 'required' => false)));
		}


		//one to many
		if($this->needsWidget('page_views_list')){
			$this->setWidget('page_views_list', new sfWidgetFormDmPaginatedDoctrineChoice(array('multiple' => true, 'model' => 'DmPageView', 'expanded' => true)));
			$this->setValidator('page_views_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmPageView', 'required' => false)));
		}
		//one to many
		if($this->needsWidget('areas_list')){
			$this->setWidget('areas_list', new sfWidgetFormDmPaginatedDoctrineChoice(array('multiple' => true, 'model' => 'DmArea', 'expanded' => true)));
			$this->setValidator('areas_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmArea', 'required' => false)));
		}





    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'DmLayout', 'column' => array('name')))
    );

    $this->widgetSchema->setNameFormat('dm_layout[%s]');

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
    return 'DmLayout';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['page_views_list']))
    {
        $this->setDefault('page_views_list', array_merge((array)$this->getDefault('page_views_list'),$this->object->PageViews->getPrimaryKeys()));
    }

    if (isset($this->widgetSchema['areas_list']))
    {
        $this->setDefault('areas_list', array_merge((array)$this->getDefault('areas_list'),$this->object->Areas->getPrimaryKeys()));
    }

  }

  protected function doSave($con = null)
  {
    $this->savePageViewsList($con);
    $this->saveAreasList($con);

    parent::doSave($con);
  }

  public function savePageViewsList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['page_views_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->PageViews->getPrimaryKeys();
    $values = $this->getValue('page_views_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('PageViews', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('PageViews', array_values($link));
    }
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
