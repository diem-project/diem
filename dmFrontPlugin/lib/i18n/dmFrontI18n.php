<?php

class dmFrontI18n extends dmI18n
{

  /**
   * Search first in requested catalogue, then in dm catalogue
   */
  public function __($string, $args = array(), $catalogue = 'messages')
  {
//    $timer = dmDebug::timerOrNull('dmI18n::__');

    $result = parent::__($string, $args, $catalogue);

    if ($result === $string && $catalogue !== 'dm')
    {
      $result = parent::__($string, $args, 'dm');
    }

//    $timer && $timer->addTime();

    return $result;
  }

}