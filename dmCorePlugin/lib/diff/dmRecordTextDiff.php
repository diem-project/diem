<?php

/*
 * Responsible for rendering diffs between two versions of a record
 */
class dmRecordTextDiff
{
  protected
  $serviceContainer,
  $fromVersion,
  $toVersion,
  $table;
  
  public function __construct(dmBaseServiceContainer $serviceContainer, Doctrine_Record $fromVersion, Doctrine_Record $toVersion)
  {
    if (get_class($fromVersion) !== get_class($toVersion))
    {
      throw new dmException('The from_version and to_version must be intances of the same class');
    }
    
    $this->serviceContainer = $serviceContainer;
    $this->fromVersion      = $fromVersion;
    $this->toVersion        = $toVersion;
    
    $this->table            = $fromVersion->getTable();
  }
  
  /*
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
  
  /*
   * @return string Diff for one field
   */
  public function getHtmlDiff($field)
  {
    $fromValue  = $this->fromVersion->get($field);
    $toValue    = $this->toVersion->get($field);
    
    if ($this->isTextField($field))
    {
//      if ($this->isMarkdownField($field))
//      {
//        $fromValue  = $this->serviceContainer->get('markdown')->toHtml($fromValue);
//        $toValue    = $this->serviceContainer->get('markdown')->toHtml($toValue);
//      }
      
      $diff = $this->generateHtmlDiff($fromValue, $toValue);
    }
    else
    {
      $diff = false;
    }
    
    return $diff;
  }
  
  protected function generateHtmlDiff($fromValue, $toValue)
  {
    return $this->serviceContainer->getService('text_diff')->generateHtml($fromValue, $toValue);
  }
  
  protected function isTextField($field)
  {
    return in_array($this->getFieldType($field), array('string', 'blob', 'clob', 'date', 'time', 'timestamp', 'enum'));
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