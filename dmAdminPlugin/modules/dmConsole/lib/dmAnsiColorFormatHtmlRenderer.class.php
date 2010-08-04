<?php
/**
 * sfAnsiColorFormatter provides methods to colorize text to be displayed on a console.
 *
 * @package    diem
 * @subpackage dmConsole
 * @author     Anton Stoychev
 */
class dmAnsiColorFormatHtmlRenderer
{

  protected static $map = array(
        1=>'bold',
        32=>'green',
        33=>'brown',
        34=>'blue',
        36=>'cyan',
        37=>'white',
        41=>'red',
        42=>'green'

  );

  public static function getStyle($code='')
  {
    if (trim($code)=='') {return '';}
    $code = intval($code);
    $value = self::$map[$code];
    return ' '.self::getProperty($code).': '.$value.'; ';
  }

  public static function getProperty($code)
  {
    if (in_array($code, range(30, 37))) {
      return 'color';
    }
    if (in_array($code, range(40, 47))) {
      return 'background';
    }
    if ($code == 1) {
      return 'font-weight';
    }
  }

  public static function render($string)
  {
    $string = preg_replace(
            "/([^\033]*)\033\[([0-9]+[0-9]?)+(;([0-9]+[0-9]?))?(;([0-9]+[0-9]?))?m([^\033]*)\033\[0m/e",
            "'\\1<span style=\"'.
              dmAnsiColorFormatHtmlRenderer::getStyle(\\2).
              dmAnsiColorFormatHtmlRenderer::getStyle(\\4).
              dmAnsiColorFormatHtmlRenderer::getStyle(\\6).
              '\">\\7</span>'",
            $string
            );
    $string = str_replace("\033[0m", '', $string);
    $string = str_replace("\033[m", '', $string);
    return stripslashes($string);
  }

}
