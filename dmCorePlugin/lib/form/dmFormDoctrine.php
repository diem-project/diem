<?php

/**
 * Diem form base class.
 *
 * @package    diem
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormBaseTemplate.php 9304 2008-05-27 03:49:32Z dwhittle $
 */
abstract class dmFormDoctrine extends sfFormDoctrine
{
  protected $nestedSetParentId = null;

  public function updateNestedSetParentIdColumn($nestedSetParentId)
  {
    $this->nestedSetParentId = $nestedSetParentId;
    // further action is handled in the save() method
  }

  public function configure() {

    if ($this->object instanceof sfDoctrineRecord && $this->object->getTable()->isNestedSet()) {
      // unset NestedSet columns
      unset($this['lft'], $this['rgt'], $this['level']);
      if ($this->object->getTable()->getTemplate('NestedSet')->getOption('hasManyRoots')) {
        unset($this[$this->object->getTable()->getTemplate('NestedSet')->getOption('rootColumnName')]);
      }

      // add sorting
      $orderBy = 'lft';
      if ($relation->getTable()->getTemplate('NestedSet')->getOption('hasManyRoots')) {
        $orderBy = $relation->getTable()->getTemplate('NestedSet')->getOption('rootColumnName', 'root_id') . ', ' . $orderBy;
      }

      $this->widgetSchema['nested_set_parent_id'] = new sfWidgetFormDoctrineChoice(array(
        'model' => get_class($this->object),
        'add_empty' => '~',
        'order_by' => array($orderBy, ''),
        'method' => 'getNestedSetIndentedName'
        ));
      $this->validatorSchema['nested_set_parent_id'] = new sfValidatorDoctrineChoice(array(
        'required' => false,
        'model' => get_class($this->object)
        ));
      $this->setDefault('nested_set_parent_id', $this->object->getNestedSetParentId());
      $this->widgetSchema->setLabel('nested_set_parent_id', 'Child of');

    }

    if ($this->object instanceof sfDoctrineRecord) {
      foreach ($this->getObject()->getTable()->getRelationHolder()->getAll() as $relation) {
        if ($relation->getTable() instanceof myDoctrineTable && $relation->getTable()->isNestedSet()) {
          // check for many to many
          $fieldname = $relation->getType()
                  ? dmString::underscore($relation->getAlias()) . '_list'
                  : $relation->getLocalColumnName()
                  ;
          if (!($this->widgetSchema[$fieldname] instanceof sfWidgetFormDoctrineChoice)) {
            $this->widgetSchema[$fieldname] = new sfWidgetFormDoctrineChoice(array('model' => $relation->getClass()));
          }

          $orderBy = 'lft';
          if ($relation->getTable()->getTemplate('NestedSet')->getOption('hasManyRoots')) {
            $orderBy = $relation->getTable()->getTemplate('NestedSet')->getOption('rootColumnName', 'root_id') . ', ' . $orderBy;
          }
          $this->widgetSchema[$fieldname]->setOptions(array_merge(
                  $this->widgetSchema[$fieldname]->getOptions(),
                  array(
                      'method' => 'getNestedSetIndentedName',
                      'order_by' => array($orderBy, ''),
            )));

        }
      }
    }

    parent::configure();

  }

  /**
   * Extend the doSave method to handle NestedSets
   * @param Doctrine_Connection $con
   */
  protected function doSave($con = null) {

    parent::doSave($con);

    if ($this->object->getTable()->isNestedSet()) {

      $node = $this->object->getNode();

      if ($this->nestedSetParentId != $this->object->getNestedSetParentId() || !$node->isValidNode()) {
        if (empty($this->nestedSetParentId)) {
          //save as a root
          if ($node->isValidNode()) {
            $node->makeRoot($this->object['id']);
            $this->object->save($con);
          } else {
            $this->object->getTable()->getTree()->createRoot($this->object); //calls $this->object->save internally
          }
        } else {
          //form validation ensures an existing ID for $this->nestedSetParentId
          $nestedSetParent = $this->object->getTable()->find($this->nestedSetParentId);
          $nestedSetMethod = ($node->isValidNode() ? 'move' : 'insert') . 'AsFirstChildOf';
          $node->$nestedSetMethod($nestedSetParent); //calls $this->object->save internally
        }
      }
    }
  }

  /**
   * Unset automatic fields like 'created_at', 'updated_at', 'position'
   */
  protected function unsetAutoFields($autoFields = null)
  {
    $autoFields = null === $autoFields ? $this->getAutoFieldsToUnset() : (array) $autoFields;
    
    foreach($autoFields as $autoFieldName)
    {
      if (isset($this->widgetSchema[$autoFieldName]))
      {
        unset($this[$autoFieldName]);
      }
    }
  }
  
  protected function getAutoFieldsToUnset()
  {
    $fields = array('created_at', 'updated_at');

    if ($this->getObject()->getTable()->isSortable())
    {
      $fields[] = 'position';
    }

    if ($this->getObject()->getTable()->isVersionable())
    {
      $fields[] = 'version';
    }

    if ($this->getObject()->getTable()->isNestedSet())
    {
      $fields[] = 'lft';
      $fields[] = 'rgt';
      $fields[] = 'level';
      if ($this->getObject()->getTable()->getTemplate('NestedSet')->getOption('hasManyRoots')) {
        $fields[] = $this->getObject()->getTable()->getTemplate('NestedSet')->getOption('rootColumnName', 'root_id');
      }
    }

    return $fields;
  }

  protected function filterValuesByEmbeddedMediaForm(array $values, $local)
  {
    $formName = $local.'_form';
     
    if (!isset($this->embeddedForms[$formName]))
    {
      return $values;
    }
    
    $isFileProvided = isset($values[$formName]['file']) && !empty($values[$formName]['file']['size']);

    // media id provided with drag&drop
    if(!empty($values[$formName]['id']) && !$isFileProvided)
    {
      if($this->embeddedForms[$formName]->getObject()->isNew() || $this->embeddedForms[$formName]->getObject()->id != $values[$formName]['id'])
      {
        if($media = dmDb::table('DmMedia')->findOneByIdWithFolder($values[$formName]['id']))
        {
          $this->embeddedForms[$formName]->setObject($media);
          $values[$formName]['dm_media_folder_id'] = $media->dm_media_folder_id;
        }
      }
    }
    // no existing media, no file, and it is not required : skip all
    elseif ($this->embeddedForms[$formName]->getObject()->isNew() && !$isFileProvided && !$this->embeddedForms[$formName]->getValidator('file')->getOption('required'))
    {
      // remove the embedded media form if the file field was not provided
      unset($this->embeddedForms[$formName], $values[$formName]);
      // pass the media form validations
      $this->validatorSchema[$formName] = new sfValidatorPass;
    }

    return $values;
  }

  protected function processValuesForEmbeddedMediaForm(array $values, $local)
  {
    $formName = $local.'_form';

    if (!isset($this->embeddedForms[$formName]))
    {
      return $values;
    }

    /*
     * We have a new file for an existing media.
     * Let's create a new media
     */
    if($values[$formName]['file'] && $values[$formName]['id'])
    {
      $values[$formName]['id'] = null;
        
      $media = new DmMedia;
      $media->Folder = $this->object->getDmMediaFolder();

      $this->embeddedForms[$formName]->setObject($media);
    }
    
    return $values;
  }

  protected function doUpdateObjectForEmbeddedMediaForm(array $values, $local, $alias)
  {
    $formName = $local.'_form';

    if (!isset($this->embeddedForms[$formName]))
    {
      return;
    }

    if (!empty($values[$formName]['remove']))
    {
      $this->object->set($alias, null);
    }
    else
    {
      $this->object->set($alias, $this->embeddedForms[$formName]->getObject());
    }
  }

  protected function mergeI18nForm($culture = null)
  {
    $this->mergeForm($this->createI18nForm());
  }
  
  /**
   * Create current i18n form
   */
  protected function createI18nForm($culture = null)
  {
    if (!$this->isI18n())
    {
      throw new dmException(sprintf('The model "%s" is not internationalized.', $this->getModelName()));
    }

    $i18nFormClass = $this->getI18nFormClass();

    $culture = null === $culture ? dmDoctrineRecord::getDefaultCulture() : $culture;
    
    // translation already set, use it
    if ($this->object->get('Translation')->contains($culture))
    {
      $translation = $this->object->get('Translation')->get($culture);
    }
    else
    {
      $translation = $this->object->get('Translation')->get($culture);
      
      // populate new translation with fallback values
      if (!$translation->exists())
      {
        if($fallback = $this->object->getI18nFallBack())
        {
          $fallBackData = $fallback->toArray();
          unset($fallBackData['id'], $fallBackData['lang']);
          $translation->fromArray($fallBackData);
        }
      }
    }

    $i18nForm = new $i18nFormClass($translation);
    
    unset($i18nForm['id'], $i18nForm['lang']);

    return $i18nForm;
  }

  /**
   * Sets the current object for this form.
   *
   * @return dmDoctrineRecord The current object setted.
   */
  public function setObject(dmDoctrineRecord $record)
  {
    return $this->object = $record;
  }
}