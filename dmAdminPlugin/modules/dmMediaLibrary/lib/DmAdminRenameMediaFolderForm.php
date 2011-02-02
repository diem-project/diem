<?php

class DmAdminRenameMediaFolderForm extends dmForm
{
  protected
  $folder;

  public function __construct($folder, $options = array(), $CSRFSecret = null)
  {
    $this->folder = $folder;

    parent::__construct(array('id' => $folder->id, 'name' => $folder->name), $options);
  }
  
  public function configure()
  {
    parent::configure();
    
    $this->widgetSchema['name'] = new sfWidgetFormInputText();
    $this->validatorSchema['name'] = new dmValidatorDirectoryName();
    
    $this->mergePostValidator(new sfValidatorAnd(array(
      new sfValidatorCallback(array('callback' => array($this, 'checkExistsInParent')))
    )));
  }
  
  public function save()
  {
    return $this->folder->rename($this->getValue('name'));
  }
  
  public function checkExistsInParent($validator, $values)
  {
    if ($values['name'] == $this->folder->name)
    {
      return $values;
    }
    
    if($this->folder->Node->getParent()->hasSubFolder($values['name']))
    {
      throw new sfValidatorErrorSchema($validator, array('name' => new sfValidatorError($validator, 'Already exists in this folder')));
    }
    
    $values['rel_path'] = $this->folder->Node->getParent()->relPath.'/'.$values['name'];

    return $values;
  }
  
}