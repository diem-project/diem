<?php

class dmFrontI18n extends dmI18n
{

  /**
   * Search first in requested catalogue, then in dm catalogue
   */
  public function __($string, $args = array(), $catalogue = 'messages')
  {
//    $timer = dmDebug::timer('dmI18n::__');

    $result = parent::__($string, $args, $catalogue);

    if ($result === $string)
    {
      $result = parent::__($string, $args, 'dm');
    }

//    $timer->addTime();

    return $result;
  }

}