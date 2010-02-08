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
    
    if ($this->isTextField($field))
    {
      $diff = $this->generateHtmlDiff($fromValue, $toValue);
    }
    else
    {
      $diff = $this->generateHtmlDiff(
        $this->__($fromValue ? 'Yes' : 'No'),
        $this->__($toValue ? 'Yes' : 'No')
      );
    }
    
    return $diff;
  }
  
  protected function generateHtmlDiff($fromValue, $toValue)
  {
    return $this->serviceContainer->getService('text_diff')->generateHtml($fromValue, $toValue);
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
  
  protected function __($text, array $args = array(), $catalogue = null)
  {
    return $this->serviceContainer->getService('i18n')->__($text, $args, $catalogue);
  }
}