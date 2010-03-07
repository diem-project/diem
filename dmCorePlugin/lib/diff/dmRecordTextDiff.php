<?php

/*
 * Responsible for rendering diffs between two versions of a record
 */
class dmRecordTextDiff extends dmConfigurable
{
  protected
  $textDiff,
  $i18n,
  $fromVersion,
  $toVersion,
  $versionModel,
  $table,
  $parentTable,
  $relatedRecordCache = array();
  
  public function __construct(dmTextDiff $textDiff, dmI18n $i18n, dmHelper $helper, array $options)
  {
    $this->textDiff         = $textDiff;
    $this->i18n             = $i18n;
    $this->helper           = $helper;

    $this->initialize($options);
  }

  protected function initialize(array $options)
  {
    $this->configure($options);
  }

  public function compare(Doctrine_Record $fromVersion, Doctrine_Record $toVersion)
  {
    if (get_class($fromVersion) !== get_class($toVersion))
    {
      throw new dmException('The from_version and to_version must be instances of the same class');
    }
    
    $this->fromVersion      = $fromVersion;
    $this->toVersion        = $toVersion;
    $this->versionModel     = get_class($fromVersion);

    $this->table            = $this->fromVersion->getTable();
    $this->parentTable      = dmDb::table(preg_replace('/^(.+)Version$/', '$1', $this->table->getComponentName()));

    return $this;
  }

  public function getDefaultOptions()
  {
    return array(
      'media_width'   => 150,
      'media_height'  => 100
    );
  }
  
  /**
   * @return array Diffs for each field
   */
  public function getHtmlDiffs(array $fields)
  {
    $diffs = array();
    
    foreach($fields as $field)
    {
      $diffs[$field] = $this->getHtmlDiff($field);
    }
    
    return $diffs;
  }
  
  /**
   * @return string Diff for one field
   */
  public function getHtmlDiff($field)
  {
    $fromValue  = $this->fromVersion->get($field);
    $toValue    = $this->toVersion->get($field);

    if($relation = $this->getLocalRelation($field))
    {
      if($diff = $this->generateHtmlDiff($fromValue, $toValue))
      {
        if($fromValue)
        {
          $diff = str_replace('>'.$fromValue.'<', '>'.$this->renderRelatedRecord($this->fromVersion, $relation).'<', $diff);
        }
        if($toValue)
        {
          $diff = str_replace('>'.$toValue.'<', '>'.$this->renderRelatedRecord($this->toVersion, $relation).'<', $diff);
        }
      }
    }
    elseif ($this->isTextField($field))
    {
      $diff = $this->generateHtmlDiff($fromValue, $toValue);
    }
    else
    {
      $diff = $this->generateHtmlDiff(
        $this->i18n->__($fromValue ? 'Yes' : 'No'),
        $this->i18n->__($toValue ? 'Yes' : 'No')
      );
    }
    
    return $diff;
  }

  public function getHtmlValues(array $fields)
  {
    $values = array();

    foreach($fields as $field)
    {
      if($relation = $this->getLocalRelation($field))
      {
        $values[$field] = $this->renderRelatedRecord($this->toVersion, $relation);
      }
      elseif ($this->isTextField($field))
      {
        $values[$field] = nl2br($this->toVersion->get($field));
      }
      else
      {
        $values[$field] = $this->i18n->__($this->toVersion->get($field) ? 'Yes' : 'No');
      }
    }

    return $values;
  }
  
  protected function generateHtmlDiff($fromValue, $toValue)
  {
    return nl2br($this->textDiff->generateHtml($fromValue, $toValue));
  }

  protected function renderRelatedRecord(Doctrine_Record $record, Doctrine_Relation $relation)
  {
    $value = $record->get($relation->getLocal());
    
    $cacheKey = md5(implode('|', array($this->versionModel, $relation->getClass(), $value)));
    
    if(isset($this->relatedRecordCache[$cacheKey]))
    {
      return $this->relatedRecordCache[$cacheKey];
    }
    
    try
    {
      if('DmMedia' === $relation->getClass())
      {
        $relatedRecord = $relation->getTable()->findOneByIdWithFolder($value);

        if($relatedRecord && $relatedRecord->isImage())
        {
          $return = $this->helper->media($relatedRecord)
          ->size($this->getOption('media_width'), $this->getOption('media_height'))
          ->render();
        }
        else
        {
          $return = $relatedRecord;
        }
      }
      else
      {
        $return = $relation->getTable()->findOneById($value);
      }
    }
    catch(Exception $e)
    {
      $return = get_class($relation->getClass()).' #'.$value;
    }

    return $this->relatedRecordCache[$cacheKey] = $return;
  }

  protected function getLocalRelation($field)
  {
    return $this->parentTable->getRelationHolder()->getLocalByColumnName($field);
  }

  protected function isTextField($field)
  {
    return in_array($this->getFieldType($field), array('string', 'blob', 'clob', 'date', 'time', 'timestamp', 'enum', 'integer', 'float'));
  }
  
  protected function isBooleanField($field)
  {
    return 'boolean' === $this->getFieldType($field);
  }
  
  protected function isMarkdownField($field)
  {
    return $this->table->isMarkdownColumn($field);
  }
  
  protected function getFieldType($field)
  {
    return dmArray::get($this->table->getColumnDefinition($field), 'type');
  }
}