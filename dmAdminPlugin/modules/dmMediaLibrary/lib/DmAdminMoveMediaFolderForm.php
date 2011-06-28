<?php

class DmAdminMoveMediaFolderForm extends dmForm
{
  protected
  $folder;

  public function __construct($folder, $options = array(), $CSRFSecret = null)
  {
    $this->folder = $folder;

    parent::__construct(array('id' => $folder->id, 'parent_id' => $folder->nodeParentId), $options);
  }
  
  public function configure()
  {
    parent::configure();

    $parentChoices = $this->getParentChoices();

    $this->widgetSchema['parent_id'] = new sfWidgetFormChoice(array(
      'choices' => $parentChoices
    ));
    $this->validatorSchema['parent_id'] = new sfValidatorChoice(array(
      'choices' => array_keys($parentChoices),
      'required' => true
    ));
    
    $this->mergePostValidator(new sfValidatorAnd(array(
      new sfValidatorCallback(array('callback' => array($this, 'checkExistsInParent')))
    )));
  }

  protected function getParentChoices()
  {
    $_parentChoices = dmDb::query('DmMediaFolder f')
    ->where('f.lft < ? OR f.rgt > ?', array($this->folder->lft, $this->folder->rgt))
    ->orderBy('f.lft')
    ->select('f.id, f.level, f.rel_path')
    ->fetchPDO();

    $parentChoices = array();
    foreach($_parentChoices as $values)
    {
      $name = basename($values[2]) ? basename($values[2]) : 'root';
      $parentChoices[$values[0]] = str_repeat('&nbsp;&nbsp;', $values[1]).'-&nbsp;'.$name;
    }

    return $parentChoices;
  }
  
  public function save()
  {
    return $this->folder->move(dmDb::table('DmMediaFolder')->find($this->getValue('parent_id')));
  }
  
  public function checkExistsInParent($validator, $values)
  {
    if ($values['parent_id'] == $this->folder->nodeParentId)
    {
      return $values;
    }

    $newParent = dmDb::table('DmMediaFolder')->find($values['parent_id']);
    
    if($newParent->hasSubFolder($this->folder->name))
    {
      throw new sfValidatorErrorSchema($validator, array('parent_id' => new sfValidatorError($validator, 'Already exists in this folder')));
    }

    return $values;
  }
  
}