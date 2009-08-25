<?php

class dmArray
{

	/*
	 * Sets each array key to its corresponding value
	 */
	public static function valueToKey($array)
	{
		$tmp = array();
		foreach($array as $value)
		{
			$tmp[$value] = $value;
		}
		unset($array);
		return $tmp;
	}

	/*
	 * Returns the value of an array, if the key exists
	 */
	public static function get($array, $key, $default = null, $defaultIfNull = false)
	{
		if (!is_array($array))
		{
			return $default;
		}

		if (false === $defaultIfNull)
		{
			if(isset($array[$key]))
			{
				return $array[$key];
			}
			else
			{
				return $default;
			}
		}

		if(!empty($array[$key]))
		{
			return $array[$key];
		}
		else
		{
			return $default;
		}
	}


  // retourne la première valeur d'un tableau
  public static function first($array)
  {
    if(!is_array($array))
    {
      return $array;
    }

    if(empty($array))
    {
      return null;
    }

    $a = array_shift($array);
    unset($array);

    return $a;
  }

  // retourne la dernière valeur d'un tableau
  public static function last($array)
  {
    if(!is_array($array))
    {
      return $array;
    }

    if(empty($array))
    {
      return null;
    }

    $a = array_pop($array);
    unset($array);

    return $a;
  }

  // retourne la première clé d'un tableau
  public static function firstKey($array)
  {
    return self::firstEntryIn(array_keys($array));
  }

  // retourne les premières valeurs d'un tableau
  public static function firsts($array, $nb)
  {
    if(!is_array($array))
    {
      return $array;
    }

    if(empty($array))
    {
      return null;
    }

    $nb = min(array($nb, count($array)));

    $return_entries = array();

    for($it = 0; $it < $nb; $it++)
    {
      if ($entry = array_shift($array))
      {
        $return_entries[] = $entry;
      }
    }

    unset($array);

    return $return_entries;
  }

  // unset empty elements of the array
  public static function unsetEmpty(array $array, array $keys)
  {
    foreach($keys as $key)
    {
      if(isset($array[$key]) && empty($array[$key]))
      {
        unset($array[$key]);
      }
    }
    
    return $array;
  }
  
  public static function toHtmlCssClasses(array $classes)
  {
  	return preg_replace('|\s{2,}|', ' ', trim(implode(' ', $classes)));
  }
}