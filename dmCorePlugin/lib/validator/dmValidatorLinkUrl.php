<?php

class dmValidatorLinkUrl extends sfValidatorUrl
{ 

  protected function configure($options = array(), $messages = array())
  {
    parent::configure($options, $messages);

    $this->setMessage('invalid', '"%value%" is not a valid link.');
  }
  
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
      elseif('#' === $value{0})
      {
        return $value;
      }
      elseif('@' === $value{0})
      {
        return $value;
      }
      
      throw $e;
    }
  }
}