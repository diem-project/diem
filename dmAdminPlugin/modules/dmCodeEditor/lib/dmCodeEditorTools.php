<?php

/**
 * dmCodeEditorTools.
 *
 * @package    diem
 * @subpackage dmCodeEditorTools
 * @author     Your name here
 * @version    SVN: $Id: dmCodeEditorTools.php 12474 2008-10-31 10:41:27Z mbeurel $
 */
class dmCodeEditorTools
{
  protected static $arrayDecodeEncode = array(
  "_-SLASH-_"  =>  "/",
  "_-DOT-_"   =>  ".",
  "_-SPACE-_" =>  " "
  );
  
  /*
   * Decode Url Tree
   * exemple : dmCodeEditorTools::decodeUrlTree(_SLASH_data_SLASH_exemple_DOT_txt) = /data/exemple.txt
   */
  public static function decodeUrlTree($dirOrFile)
  {
    return strtr($dirOrFile, self::getArrayDecodeEncode());
  }
  
  /*
   * Encode Url Tree
   * exemple : dmCodeEditorTools::encodeUrlTree(/data/exemple.txt) = _SLASH_data_SLASH_exemple_DOT_txt
   */
  public static function encodeUrlTree($dirOrFile)
  {
    return strtr($dirOrFile, array_flip(self::getArrayDecodeEncode(true)));
  }
  
   /*
   * Encode Url Tree
   * exemple : dmCodeEditorTools::decodeUrlTreeForCopy(_SLASH_data_SLASH_exemple_SPACE_file_DOT_txt) = /data/exemple\ file.txt
   */
  public static function decodeUrlTreeForCopy($dirOrFile)
  {
    return str_replace(" ", "\ ", self::decodeUrlTree($dirOrFile));
  }
  
  public static function getArrayDecodeEncode($root_dir = false)
  {
  	$array = self::$arrayDecodeEncode;
  	
    if($root_dir)
    {
      $array[''] = sfConfig::get('sf_root_dir');
    }
    
    return $array;
  }
  

}