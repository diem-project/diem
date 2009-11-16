<?php

class DmAdminNewMediaFolderForm extends DmMediaFolderForm
{
  
  public function configure()
  {
    parent::configure();
    
    $this->widgetSchema['name'] = new sfWidgetFormInputText();
    
    $this->validatorSchema['name'] = new sfValidatorString();
    
    $this->widgetSchema['parent_id'] = new sfWidgetFormInputHidden();
    
    $this->validatorSchema['parent_id'] = new sfValidatorDoctrineChoice(array('model' => 'DmMediaFolder'));
    
    $this->useFields(array('name'));
    
    $this->mergePostValidator(new sfValidatorAnd(array(
      new sfValidatorCallback(array('callback' => array($this, 'checkName'))),
      new sfValidatorCallback(array('callback' => array($this, 'checkExistsInParent')))
    )));
  }
  
  protected function doUpdateObject($values)
  {
    parent::doUpdateObject($values);
    
    if (!$values['parent'] instanceof DmMediaFolder)
    {
      throw new dmException('Create folder with unknown parent '.$values['parent_id']);
    }
    
    $this->object->Node->insertAsLastChildOf($values['parent']);
  }
  
  public function checkName($validator, $values)
  {
    if ($values['name'] !== DmMediaFolder::sanitizeName($values['name']))
    {
      // throw an error bound to the password field
      throw new sfValidatorErrorSchema($validator, array('name' => new sfValidatorError($validator, 'Invalid.')));
    }
    
    return $values;
  }
  
  public function checkExistsInParent($validator, $values)
  {
    if (!$values['parent'] = dmDb::table('DmMediaFolder')->find($values['parent_id']))
    {
      throw new dmException('Create folder without parent');
    }
    
    if(dmDb::table('DmMediaFolder')->findOneByRelPath($values['parent']->relPath.'/'.$values['name']))
    {
      throw new sfValidatorErrorSchema($validator, array('name' => new sfValidatorError($validator, 'Already exists in this folder')));
    }
    
    $values['rel_path'] = $values['parent']->relPath.'/'.$values['name'];

    return $values;
  }
  
}