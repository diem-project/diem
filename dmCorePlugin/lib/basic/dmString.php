<?php

class dmString extends sfInflector
{
	const PARTS_SEPARATOR = '.';
	const SEPARATOR = '__DM_SPLIT__';

	protected static
	  $accentsReplacements = array(
	    '¥' => 'Y', 'µ' => 'u', 'À' => 'A', 'Á' => 'A',
	    'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A',
	    'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E',
	    'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I',
	    'Î' => 'I', 'Ï' => 'I', 'Ð' => 'D', 'Ñ' => 'N',
	    'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O',
	    'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U',
	    'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'ß' => 's',
	    'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a',
	    'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c',
	    'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
	    'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
	    'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o',
	    'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o',
	    'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u',
	    'ý' => 'y', 'ÿ' => 'y'
	  ),
	  $camelizeCache = array();

	/*
	 * Clean dirty strings
	 */
	public static function unixify($text)
  {
    return strtr($text, array(
        "\r"      => ''      // réparation des sauts de ligne mac/windows
      , "\t"      => '  '    // tabs -> double espace
      , "&#8217;" => "'"     // apostrophe micro$oft -> '
      , '“'       => '&lquot;'
      , '”'       => '&rquot;'
      , '®'       => '&reg;'
      , '‘'       => '&lsquo;'
      , '’'       => '&rsquo;'
    ));
  }

	/*
	 * Separate 2 parts if concatenated with self::PARTS_SEPARATOR
	 * part1, part2 => part1, part2
	 * part1.part2, null => part1, part2
	 */
	public static function separate($part1, $part2 = null)
	{
		if(is_array($part1))
		{
			return $part1;
		}
    if ($part2 === null && strpos($part1, self::PARTS_SEPARATOR))
    {
      list($part1, $part2) = explode(self::PARTS_SEPARATOR, $part1);
    }
    return array($part1, $part2);
	}

	/*
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

  /*
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

  /*
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
//        return $something->getModel();
      }
      return get_class($something);
    }

    if (!is_string($something))
    {
      return null;
    }

//    return preg_replace(
//      '/(^|_)(.)/e',
//      "strtoupper('\\2')",
//      $something
//    );

    if (!isset(self::$camelizeCache[$something]))
    {
      self::$camelizeCache[$something] = preg_replace(
	      '/(^|_)(\w)/e',
	      "strtoupper('\\2')",
	      $something
	    );
    }

    return self::$camelizeCache[$something];
  }

  public static function humanize($text)
  {
  	return parent::humanize(self::underscore($text));
  }

  /*
   * Transform any text into a valid slug
   * @return string slug
   */
  public static public function slugify($text, $preserveSlashes = false)
  {
  	if ($preserveSlashes)
  	{
  		$text = str_replace('/', '_s_l_a_s_h_', $text);
  	}
  	
  	$text = self::removeAccents($text);

  	// strip all non word chars
    $text = preg_replace('/\W/', ' ', $text);

    // replace all white space sections with a dash
    $text = preg_replace('/\ +/', '-', $text);

    // trim dashes
//    $text = preg_replace('/\-$/', '', $text);
//    $text = preg_replace('/^\-/', '', $text);

    // trim and lowercase
    $text = strtolower(trim($text, '-'));
    
    if ($preserveSlashes)
    {
      $text = str_replace('_s_l_a_s_h_', '/', $text);
    }

    return $text;
  }
  
  /*
   * Transform a slug into a human readable text with blank spaces
   * @return string text
   */
  public static function unSlugify($slug)
  {
  	return str_replace('-', ' ', $slug);
  }

  public static function removeAccents($text)
  {
    return strtr($text, self::$accentsReplacements);
  }

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

    // DMS STYLE - string opt in name
    self::retrieveOptFromString($string, $array);

    if ($implodeClasses && isset($array['class']))
    {
    	$array['class'] = implode(' ', $array['class']);
    }

    return $array;
	}

  // retire les attributs css de $string et les met dans le tableau $opt
  // div#id.class devient div, array('id'=>'id', 'class'=>'class')
  public static function retrieveCssFromString(&$string, &$opt)
  {
    if (empty($string))
    {
      return null;
    }

    if (strpos($string, '#') !== false)
    {
      // récupération de l'id
      preg_match('/#([\w\-_]*)/', $string, $id);
      if (isset($id[1]))
      {
        $opt['id'] = $id[1];
        $string = str_replace('#'.$id[1], '', $string);
      }
    }

    if (strpos($string, '.') !== false)
    {
      // récupération des classes
      preg_match_all('/\.([\w\-_]*)/', $string, $classes);
      if (isset($classes[1]))
      {
        if (!isset($opt['class']))
        {
          $opt['class'] = array();
        }
        $opt['class'] = array_merge($opt['class'], $classes[1]);

        $string = str_replace('.'.implode('.', $classes[1]), '', $string);
      }
    }
  }

  public static function retrieveOptFromString(&$string, &$opt)
  {
    if (empty($string))
    {
      return null;
    }

    $opt = array_merge(
      $opt,
      sfToolkit::stringToArray($string)
    );
    $string = '';
  }

	/*
	 * Returns a random string
	 */
  public static function random($length = 8)
  {
    $val = '';
    $values = 'abcdefghijklmnopqrstuvwxyz0123456789';
    for ( $i = 0; $i < $length; $i++ )
    {
      $val .= $values[mt_rand( 0, 35 )];
    }
    return $val;
  }

  public static function truncate($text, $length = 30, $truncate_string = '...', $truncate_lastspace = false)
  {
    if (empty($text))
    {
      return '';
    }


    if(is_array($text))
    {
    	throw new dmException($text);
    }

    $text = (string) $text;

    if($mbstring = extension_loaded('mbstring'))
    {
      mb_internal_encoding('UTF-8');
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
      $truncate_text = $substr($text, 0, $length - $strlen($truncate_string));

      if ($truncate_lastspace)
      {
        $truncate_text = preg_replace('/\s+?(\S+)?$/', '', $truncate_text);
      }

      return $truncate_text.$truncate_string;
    }
    else
    {
      return $text;
    }
  }

  public static function encode($value)
  {
    if (is_array($value))
    {
      $value = implode(self::SEPARATOR, $value);
    }
    
    return base64_encode($value);
  }
  
  public static function decode($coded_value)
  {
    $value = base64_decode($coded_value);
    
    if (strpos($value, self::SEPARATOR) !==false)
    {
      $value = explode(self::SEPARATOR, $value);
    }
    
    return $value;
  }
  
}