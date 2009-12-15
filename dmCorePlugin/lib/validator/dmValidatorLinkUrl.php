<?php

class dmValidatorLinkUrl extends sfValidatorUrl
{
  /**
   * @see sfValidatorUrl
   */
  protected function doClean($value)
  {
    try
    {
      return parent::doClean($value);
    }
    catch(sfValidatorError $e)
    {
      if (strncmp($value, 'page:', 5) === 0 && dmDb::table('DmPage')->findOneBySource($value))
      {
        return $value;
      }
      elseif (strncmp($value, 'media:', 6) === 0 && dmDb::table('DmMedia')->findOneByIdWithFolder(substr($value, 6)))
      {
        return $value;
      }
      
      throw $e;
    }
  }
}