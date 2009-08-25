<?php

class dmYaml extends sfYaml
{
  const TWO_DOTS = "(?dmTd)";
  const SHARP    = "(?dmSharp)";

  public static function load($input)
  {
    return str_replace(
      array(self::TWO_DOTS, self::SHARP),
      array(":", "#"),
      parent::load($input)
    );
  }

  public static function dump($array, $inline = 1)
  {
    return parent::dump(str_replace(
      array(":", "#"),
      array(self::TWO_DOTS, self::SHARP),
      $array
    ), $inline);
  }
}