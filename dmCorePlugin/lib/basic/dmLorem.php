<?php

class dmLorem
{
  protected static
  $loremText,
  $markdownLoremText;

  public static function getMarkdownLorem($nbParagraphs = 1)
  {
    return str_repeat(self::getMarkdownLoremText(), $nbParagraphs);
  }

  public static function getBigLorem($nbParagraphs = null)
  {
    $lorem = self::getLoremText();

    if (null === $nbParagraphs)
    {
      $nbParagraphs = 1;
    }

    $paragraphs = array();
    for($it=0; $it<$nbParagraphs; $it++)
    {
      $paragraphs[] = dmArray::get($lorem, array_rand($lorem));
    }

    return implode("\n", $paragraphs);
  }

  public static function getLittleLorem($nbCarac = null, $maxNbCarac = 255)
  {
    if (!$nbCarac)
    {
      $nbCarac = 5 + rand(0, 60);
    }

    $nbCarac = min($nbCarac, $maxNbCarac);

    $paragraph = self::getBigLorem(1);

    return substr($paragraph, rand(0, strlen($paragraph)-$nbCarac), $nbCarac);
  }

  protected static function getLoremText()
  {
    if (null === self::$loremText)
    {
      self::$loremText = file(dmOs::join(sfConfig::get("dm_core_dir"), "data/lorem/big"));
    }

    return self::$loremText;
  }

  protected static function getMarkdownLoremText()
  {
    if (null === self::$markdownLoremText)
    {
      self::$markdownLoremText = implode('', file(dmOs::join(sfConfig::get("dm_core_dir"), "data/lorem/markdown")));
    }

    return self::$markdownLoremText;
  }

}