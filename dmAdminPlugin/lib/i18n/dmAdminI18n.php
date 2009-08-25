<?php

class dmAdminI18n extends dmI18n
{

  /**
   * Search first in dm catalogue, then in requested catalogue
   */
  public function __($string, $args = array(), $catalogue = 'messages')
  {
    $timer = dmDebug::timer('dmI18n::__');

    $result = parent::__($string, $args, 'dm');

    if ($result === $string)
    {
      $result = parent::__($string, $args, $catalogue);
    }

    $timer->addTime();

    return $result;
  }

}