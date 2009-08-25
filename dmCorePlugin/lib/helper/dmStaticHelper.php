<?php

class dmStaticHelper
{
	/*
	 * a, class='tagada ergrg' id=zegf, contenu
	 * a class=tagada id=truc, contenu
	 * a, contenu
	 * a, array(), contenu
	 * a#truc.tagada, contenu
	 */
	public static function £o($name, array $opt = array())
	{
		return self::£($name, $opt, false, false);
	}

	public static function £c($name)
	{
		if (($pos = strpos($name, '.')) !== false)
		{
			$classes = substr($name, $pos+1);
			$name = substr($name, 0, $pos);
			if (strpos($classes, 'beafh') !== false || strpos($classes, 'beafv') !== false)
			{
				if (in_array($name, array('span', 'a', 'p')))
				{
					$beafTag = 'span';
				}
				else
				{
					$beafTag = 'div';
				}
				return '</'.$beafTag.'><'.$beafTag.' class="beafter"></'.$beafTag.'></'.$name.'>';
			}
		}
		return '</'.$name.'>';
	}

	public static function £($name, $opt = array(), $content = false, $openAndClose = true)
	{
		if (!($name = trim($name)))
		{
			return '';
		}

		$tagOpt = array();

		// séparation du nom du tag et des attributs dans $name
		if ($first_space_pos = strpos($name, ' '))
		{
			$name_opt = substr($name, $first_space_pos + 1);
			$name = substr($name, 0, $first_space_pos);

			// DMS STYLE - string opt in name
			dmString::retrieveOptFromString($name_opt, $tagOpt);
		}

		// JQUERY STYLE - css expression
		dmString::retrieveCssFromString($name, $tagOpt);

		// ARRAY STYLE - array opt

		if (is_array($opt) && !empty($opt))
		{
			if (isset($opt["json"]))
			{
				$tagOpt['class'][] = json_encode($opt["json"]);
				unset($opt["json"]);
			}
			if (isset($opt['class']))
			{
				$tagOpt['class'][] = is_array($opt['class']) ? implode(' ', $opt['class']) : $opt['class'];
				unset($opt['class']);
			}
			$tagOpt = array_merge(
			$tagOpt,
			$opt
			);
		}

		// SYMFONY STYLE - string opt

		elseif (is_string($opt) && $content)
		{
			$opt = sfToolkit::stringToArray($opt);
			if (isset($opt['class']))
			{
				$tagOpt['class'][] = explode(' ', $opt['class']);
				unset($opt['class']);
			}
			$tagOpt = array_merge(
			$tagOpt,
			$opt
			);
		}

		if (!$content) // Pas de content
		{
			if (!is_array($opt))
			{
				$content = $opt;
			}
			else // Pas de opt
			{
				if ($name === "span")
				{
					$content = "&nbsp;";
				}
				else
				{
					$content = null;
				}
			}
		}

		$class = isset($tagOpt['class']) ? $tagOpt['class'] : array();

		if (in_array("beafh", $class) || in_array("beafv", $class))
		{
			$isBeaf = true;
			$tagOpt['class'][] = "clearfix";
			$beafTag = in_array($name, array("span", "a", "p")) ? 'span' : 'div';
		}
		else
		{
			$isBeaf = false;
		}

		if(isset($tagOpt['lang']))
		{
			if($tagOpt['lang'] == dm::getUser()->getCulture())
			{
				unset($tagOpt['lang']);
			}
		}

		if (isset($tagOpt['class']) && is_array($tagOpt['class']))
		{
			$tagOpt['class'] = implode(' ', array_unique($tagOpt['class']));
		}

		$optHtml = '';
		foreach ($tagOpt as $key => $val)
		{
			$optHtml .= ' '.$key.'="'.htmlentities($val, ENT_COMPAT, 'UTF-8').'"';
		}

		if ($openAndClose === true)
		{
			if ($isBeaf)
			{
				$tag = '<'.$name.$optHtml.'><'.$beafTag.' class="beafore"></'.$beafTag.'><'.$beafTag.' class="beafin">'.$content.'</'.$beafTag.'><'.$beafTag.' class="beafter"></'.$beafTag.'></'.$name.'>';
			}
			else
			{
				$tag = '<'.$name.$optHtml.'>'.$content.'</'.$name.'>';
			}
		}
		else
		{
			if ($isBeaf)
			{
				$tag = '<'.$name.$optHtml.'><'.$beafTag.' class="beafore"></'.$beafTag.'><'.$beafTag.' class="beafin">';
			}
			else
			{
				$tag = '<'.$name.$optHtml.'>';
			}
		}

		return $tag;
	}
}