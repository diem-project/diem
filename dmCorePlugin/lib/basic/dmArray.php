<?php

class dmArray
{

	/**
	 * Sets each array key to its corresponding value
	 *
	 * @param array $array source array
	 *
	 * @return array new array
	 */
	public static function valueToKey(array $array)
	{
		$tmp = array();
		foreach($array as $value)
		{
			$tmp[$value] = $value;
		}
		unset($array);
		return $tmp;
	}

	/**
	 * Returns the value of an array, if the key exists
	 *
	 * @param array      $array          source array
	 * @param int|string $key            key to get
	 * @param mixed      $default        default
	 * @param boolean    $defaultIfEmpty activate check for if empty
	 *
	 * @return mixed
	 */
	public static function get($array, $key, $default = null, $defaultIfEmpty = false)
	{
		if (!is_array($array))
		{
			return $default;
		}

		if (false === $defaultIfEmpty)
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

	/**
	 * get first value of an array
	 *
	 * @param array $array source array
	 *
	 * @return mixed first value
	 */
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

	/**
	 * get last value of an array
	 *
	 * @param array $array source array
	 *
	 * @return mixed last value
	 */
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

	/**
	 * get first n values of an array
	 *
	 * @param array $array source array
	 * @param int   $nb    how many values
	 *
	 * @return array the first n values
	 */
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

		$return_entries = array_slice($array, 0, $nb);
		unset($array);

		return $return_entries;
	}

	/**
	 * unset empty elements of the array
	 *
	 * @param array $array array to clean
	 * @param array $keys  keys to clean
	 *
	 * @return array cleaned array
	 */
	public static function unsetEmpty(array $array, array $keys)
	{
		foreach($keys as $key)
		{
			if(empty($array[$key]) && array_key_exists($key, $array))
			{
				unset($array[$key]);
			}
		}

		return $array;
	}

	/**
	 * transform an array of string to a css classes string
	 *
	 * @param array $classes single class or multi class values allowed
	 *
	 * @return string cleaned value
	 */
	public static function toHtmlCssClasses(array $classes)
	{
		return implode(' ', array_unique(array_filter(array_map('trim', $classes))));
	}

	/**
	 * Merges two arrays without reindexing numeric keys.
	 *
	 * @param array $array1 An array to merge
	 * @param array $array2 An array to merge
	 *
	 * @return array The merged array
	 */
	static public function deepArrayUnion($array1, $array2)
	{
		foreach ($array2 as $key => $value)
		{
			if (is_array($value) && isset($array1[$key]) && is_array($array1[$key]))
			{
				$array1[$key] = self::deepArrayUnion($array1[$key], $value);
			}
			else
			{
				$array1[$key] = $value;
			}
		}

		return $array1;
	}

	/**
	 * Create an array of integers using $idKey from $array
	 * Usefull when you want an array of all ids from a
	 * dmDoctrineCollection for example.
	 *
	 * @param stirng $idKey
	 * @param array $array
	 *
	 * @return array
	 */
	static public function toIds($idKey, $array)
	{
		$ids = array();
		foreach($array as $el)
		{
			$ids[] = $el[$idKey];
		}
		return $ids;
	}

	/**
	 * Removes $key from $array
	 * Can search for $value within $array as $key
	 *
	 * @param array $array
	 * @param mixed $key
	 * @param boolean $search
	 */
	static public function remove(&$array, $key, $search = false)
	{
		if(!$search)
		{
			$keys = (array) $key;
			foreach($keys as $key)
			{
				unset($array[$key]);
			}
		}
		else
		{
			$keys = (array) $key;
			foreach($keys as $key)
			{
				 $key = array_search((string) $key, $array);
				 if($key !== false) unset($array[$key]);
			}
		}
		return $array;
	}
}
