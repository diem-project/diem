<?php

class dmString extends sfInflector
{
  const ENCODING_SEPARATOR = '__DM_SPLIT__';

  protected static
    $camelizeCache = array();

  public static function escape($text, $quoteStyle = ENT_QUOTES)
  {
    return htmlspecialchars($text, $quoteStyle, 'UTF-8');
  }
  
  /**
   * Clean dirty strings
   */
  public static function unixify($text)
  {
    return strtr($text, array(
        "\r"      => ''
      , "\t"      => '  '
      , "&#8217;" => "'"
      , '“'       => '&lquot;'
      , '”'       => '&rquot;'
      , '®'       => '&reg;'
      , '‘'       => '&lsquo;'
      , '’'       => '&rsquo;'
    ));
  }

  /**
   * Adds a final 's'
   */
  public static function pluralize($word)
  {
    return $word[strlen($word)-1] == 's' ? $word : $word.'s';
  }

  public static function pluralizeNb($word, $nb, $specialPlural = false)
  {
    if($specialPlural)
    {
      return $nb > 1 ? $specialPlural : $word;
    }
    else
    {
      return $nb > 1 ? self::pluralize($word) : $word;
    }
  }

  /**
   * Returns a module formatted string
   * ModuleName => moduleName
   * module_name => moduleName
   */
  public static function modulize($something)
  {
    if ($model = self::camelize($something))
    {
      $model[0] = strtolower($model[0]);
    }
    
    return $model;
  }

  /**
   * Returns a camelized string from a lower case and underscored string by
   * upper-casing each letter preceded by an underscore.
   * modelName => ModelName
   * model_name => ModelName
   */
  public static function camelize($something)
  {
    if (is_object($something))
    {
      if ($something instanceof dmModule)
      {
        throw new dmException('dmModule should not be camelized');
      }
      
      return get_class($something);
    }

    if (!is_string($something))
    {
      if (empty($something))
      {
        return '';
      }
      
      throw new dmException('Can not camelize '.$something);
    }
    
    if (isset(self::$camelizeCache[$something]))
    {
      return self::$camelizeCache[$something];
    }

    return self::$camelizeCache[$something] = preg_replace(
      '/_(\w)/e',
      "strtoupper('\\1')",
      ucfirst($something)
    );
  }

  public static function humanize($text)
  {
    return parent::humanize(self::underscore($text));
  }

  /**
   * Transform any text into a valid slug
   * @return string slug
   */
  public static function slugify($text, $preserveSlashes = false)
  {
    if ($preserveSlashes)
    {
      $text = str_replace('/', '_s_l_a_s_h_', $text);
    }
    
    $text = self::transliterate($text);

    // strip all non word chars
    // replace all white space sections with a dash
    $text = preg_replace(array('/\W/', '/\s+/'), array(' ', '-'), $text);

    // trim and lowercase
    $text = strtolower(trim($text, '-'));
    
    if ($preserveSlashes)
    {
      $text = str_replace('_s_l_a_s_h_', '/', $text);
    }

    return $text;
  }
  
  /**
   * Transform a slug into a human readable text with blank spaces
   * @return string text
   */
  public static function unSlugify($slug)
  {
    return str_replace('-', ' ', $slug);
  }

  public static function transliterate($text)
  {
    if (!preg_match('/[\x80-\xff]/', $text))
    {
      return $text;
    }

    if(!sfConfig::get('dm_string_transliteration'))
    {
      sfConfig::set('dm_string_transliteration', include(dmOs::join(sfConfig::get('dm_core_dir'), 'data/dm/transliteration/default.php')));
    }
    
    $text = strtr($text, sfConfig::get('dm_string_transliteration'));

//    if (function_exists('iconv'))
//    {
//      $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
//    }
    
    return $text;
  }
  
  /**
   * Transform string options to array options
   * Symfony and jQuery styles are accepted
   * e.g. "#an_id.a_class.another_class an_option=a_value"
   * results in array(
   *    id => an_id
   *    class => array(a_class, another_class)
   *    an_option => a_value
   *  )
   * @return array options
   */
  public static function toArray($string, $implodeClasses = false)
  {
    if(is_array($string))
    {
      return $string;
    }

    if (empty($string))
    {
      return array();
    }

    $array = array();

    // JQUERY STYLE - css expression
    self::retrieveCssFromString($string, $array);

    // SYMFONY STYLE - string opt in name
    self::retrieveOptFromString($string, $array);

    if ($implodeClasses && isset($array['class']))
    {
      $array['class'] = implode(' ', $array['class']);
    }

    return $array;
  }

  /**
   * Transform css options to array options
   * e.g. "#an_id.a_class.another_class"
   * results in array(
   *    id => an_id
   *    class => array(a_class, another_class)
   *  )
   * only expressions before the first space are taken into account
   * @return array options
   */
  public static function retrieveCssFromString(&$string, &$opt)
  {
    if (empty($string))
    {
      return null;
    }
    
    $string = trim($string);

    $firstSpacePos = strpos($string, ' ');
    
    $firstSharpPos = strpos($string, '#');
    
    // if we have a # before the first space
    if (false !== $firstSharpPos && (false === $firstSpacePos || $firstSharpPos < $firstSpacePos))
    {
      // fetch id
      preg_match('/#([\w\-]*)/', $string, $id);
      if (isset($id[1]))
      {
        $opt['id'] = $id[1];
        $string = self::str_replace_once('#'.$id[1], '', $string);
        
        if (false != $firstSpacePos)
        {
          $firstSpacePos = $firstSpacePos - strlen($id[1]) - 1;
        }
      }
    }
    
    // while we find dots in the string
    while(false !== ($firstDotPos = strpos($string, '.')))
    {
      // if the string contains a space, and the dot is after this space, then it's not a class
      if (false !== $firstSpacePos && $firstDotPos > $firstSpacePos)
      {
        break;
      }
      
      // fetch class
      preg_match('/\.([\w\-]*)/', $string, $class);
      
      if (isset($class[1]))
      {
        if (isset($opt['class']))
        {
          $opt['class'][] = $class[1];
        }
        else
        {
          $opt['class'] = array($class[1]);
        }
        
        if (false != $firstSpacePos)
        {
          $firstSpacePos = $firstSpacePos - strlen($class[1]) - 1;
        }
      }
      
      $string = self::str_replace_once('.'.$class[1], '', $string);
    }
  }

  public static function retrieveOptFromString(&$string, &$opt)
  {
    if (empty($string))
    {
      return null;
    }

    $opt = array_merge($opt, sfToolkit::stringToArray($string));
    
    $string = '';
  }

  /**
   * Returns a random string
   */
  public static function random($length = 8)
  {
    $val = '';
    $values = 'abcdefghijklmnopqrstuvwxyz0123456789';
    for ( $i = 0; $i < $length; $i++ )
    {
      $val .= $values[rand( 0, 35 )];
    }
    
    return $val;
  }

  public static function truncate($text, $length = 30, $truncateString = '...', $truncateLastspace = false)
  {
    if(!is_string($text))
    {
      throw new dmException('Can not truncate a non-string: '.$text);
    }

    $text = (string) $text;

    if(extension_loaded('mbstring'))
    {
      $strlen = 'mb_strlen';
      $substr = 'mb_substr';
    }
    else
    {
      $strlen = 'strlen';
      $substr = 'substr';
    }

    if ($strlen($text) > $length)
    {
      $text = $substr($text, 0, $length - $strlen($truncateString));

      if ($truncateLastspace)
      {
        $text = preg_replace('/\s+?(\S+)?$/', '', $text);
      }

      $text = $text.$truncateString;
    }
      
    return $text;
  }

  public static function encode($value)
  {
    if (is_array($value))
    {
      $value = implode(self::ENCODING_SEPARATOR, $value);
    }
    
    return base64_encode($value);
  }
  
  public static function decode($coded_value)
  {
    $value = base64_decode($coded_value);
    
    if (strpos($value, self::ENCODING_SEPARATOR) !== false)
    {
      $value = explode(self::ENCODING_SEPARATOR, $value);
    }
    
    return $value;
  }
  
  public static function getBaseFromUrl($url)
  {
    if ($pos = strpos($url, '?'))
    {
      return substr($url, 0, $pos);
    }

    return $url;
  }

  public static function getDataFromUrl($url)
  {
    if ($pos = strpos($url, '?'))
    {
      parse_str(str_replace('&amp;', '&', substr($url, $pos + 1)), $params);
      return $params;
    }

    return array();
  }
  
  /**
   * Returns a valid hex color uppercased without first #,
   * or null if not possible
   */
  public static function hexColor($color)
  {
    if (preg_match('|^#?[\dA-F]{6}$|i', $color))
    {
      return strtoupper(trim($color, '#'));
    }
    
    return null;
  }
  
  public static function lcfirst($string)
  {
    if (!empty($string))
    {
      $string{0} = strtolower($string{0});
    }
    
    return $string;
  }
  
  /**
   * replace $search by $replace in $subject, only once
   */
  public static function str_replace_once($search, $replace, $subject)
  {
    $firstChar = strpos($subject, $search);
    
    if($firstChar !== false)
    {
      return substr($subject,0,$firstChar).$replace.substr($subject, $firstChar + strlen($search));
    }
    else
    {
      return $subject;
    }
  }
  
  /**
   * Convert a shorthand byte value from a PHP configuration directive to an integer value
   * @param    string   $value
   * @return   int
   */
  public static function convertBytes( $value )
  {
    if ( is_numeric( $value ) )
    {
      return $value;
    } 
    else
    {
      $valueLength = strlen( $value );
      $qty = substr( $value, 0, $valueLength - 1 );
      $unit = strtolower( substr( $value, $valueLength - 1 ) );
      
      switch ( $unit )
      {
        case 'k':
          $qty *= 1024;
          break;
        case 'm':
          $qty *= 1048576;
          break;
        case 'g':
          $qty *= 1073741824;
          break;
      }
      
      return $qty;
    }
  }
}