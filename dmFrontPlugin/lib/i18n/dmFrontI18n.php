<?php

class dmFrontI18n extends dmI18n
{

  /**
   * Search first in requested catalogue, then in dm catalogue
   */
  public function __($string, $args = array(), $catalogue = 'messages')
  {
    /**
     * If null is passed as $catalogue, the default value 'messages' is not set! Why?
     * Solution: -> force set to 'messages' if no value (null, 0, '', false, ..)
     */
    if (!$catalogue) {
      $catalogue = 'messages';
    }

    $result = $this->__orFalse($string, $args, $catalogue);

    if (false === $result && $catalogue !== 'dm')
    {
      $result = $this->__orFalse($string, $args, 'dm');
    }

    if (false === $result)
    {
      $result = $this->handleNotFound($string, $args, $catalogue);
    }

    return $result;
  }

}