<?php

class dmAdminI18n extends dmI18n
{

  /**
   * Search first in dm catalogue, then in requested catalogue
   */
  public function __($string, $args = array(), $catalogue = 'messages')
  {
    $result = $this->__orFalse($string, $args, 'dm');

    if (false === $result && $catalogue !== 'dm')
    {
      $result = $this->__orFalse($string, $args, $catalogue);
    }
    
    if (false === $result)
    {
      $result = $this->handleNotFound($string, $args, $catalogue);
    }
    
    return $result;
  }

}