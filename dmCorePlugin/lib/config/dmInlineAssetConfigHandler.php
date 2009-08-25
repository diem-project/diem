<?php

require_once(dirname(__FILE__)."/dmInlineConfigHandler.php");

class dmInlineAssetConfigHandler extends dmInlineConfigHandler
{

  /**
   * Gets values from the configuration array.
   *
   * @param string $prefix    The prefix name
   * @param string $category  The category name
   * @param mixed  $keys      The key/value array
   *
   * @return array The new key/value array
   */
  protected function getValues($prefix, $category, $keys)
  {
    // loop through all key/value pairs
    foreach ($keys as $key => $value)
    {
      $values[$prefix.$this->separator.$category.$this->separator.$key] = dmAsset::getPathFromWebDir($prefix, $category.'.'.$value);
    }

    return $values;
  }
}