<?php

class dmCssMinifier
{

  // remove comments, tabs, spaces, newlines, etc.
  public static function transform($style)
  {
    return self::minify2($style);
  }

  protected static function minify1($v)
  {
    return str_replace(
    array("\r\n", "\r", "\n", "\t", '  ', '    ', '    ', '     '),
    ' ',
    preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $v)
    );
  }

  protected static function minify2($v)
  {
    return str_replace("\n", null,
      preg_replace(array("/\\;\s/", "/\s+\{\\s+/", "/\\:\s+\\#/", "/,\s+/i", "/\\:\s+\\\'/i", "/\\:\s+([0-9]+|[A-F]+)/i"), array(';', '{', ':#', ',', ":\'", ":$1"),
        preg_replace(array("/\/\*[\d\D]*?\*\/|\t+/", "/\s+/", "/\}\s+/"), array(null, ' ', "}\n"),
          str_replace("\r\n", "\n", trim($v))
        )
      )
    );
  }
}