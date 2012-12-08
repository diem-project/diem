<?php

require_once(realpath(dirname(__FILE__).'/vendor').'/JsMinEnh.php');

class dmJsMinifier
{
  
  public static function transform($script)
  {
    return self::minify1($script);
  }
  
  protected static function minify1($script)
  {
    $minifier = new JsMinEnh($script);
    return $minifier->minify();
  }
}