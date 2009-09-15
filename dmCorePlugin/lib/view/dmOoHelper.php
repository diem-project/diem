<?php

class dmOoHelper
{
	protected
	$context;

	public function link($source = null)
	{
	  switch(sfConfig::get('dm_context_type'))
	  {
      case 'admin': $link = dmAdminLinkTag::build($source); break;
      case 'front': $link = dmFrontLinkTag::build($source); break;
      default:      throw new dmException('Can not create link outside front or admin context');
	  }
	  
	  return $link;
	}
	
	public function __construct(sfContext $context)
	{
		$this->context = $context;
	}

  public function renderPartial($moduleName, $actionName, $vars = array())
  {
  	/*
  	 * partial -> _partial
  	 * dir/partial -> dir/partial
  	 */
    if (!strpos($actionName, '/'))
    {
      $actionName = '_'.$actionName;
    }

    $class = sfConfig::get('mod_'.strtolower($moduleName).'_partial_view_class', 'sf').'PartialView';
    $view = new $class($this->context, $moduleName, $actionName, '');
    $view->setPartialVars($vars);

    return $view->render();
  }

  public function renderComponent($moduleName, $componentName, $vars = array())
  {
    $this->context->getConfiguration()->loadHelpers('Partial');
    
    return get_component($moduleName, $componentName, $vars);
  }
  
  /*
   * a, class='tagada ergrg' id=zegf, contenu
   * a class=tagada id=truc, contenu
   * a, contenu
   * a, array(), contenu
   * a#truc.tagada, contenu
   */
  public static function £o($tagName, array $opt = array())
  {
    return self::£($tagName, $opt, false, false);
  }

  public static function £c($tagName)
  {
    if (($pos = strpos($tagName, '.')) !== false)
    {
      $classes = substr($tagName, $pos+1);
      $tagName = substr($tagName, 0, $pos);
      if (strpos($classes, 'beafh') !== false || strpos($classes, 'beafv') !== false)
      {
        if (in_array($tagName, array('span', 'a', 'p')))
        {
          $beafTag = 'span';
        }
        else
        {
          $beafTag = 'div';
        }
        return '</'.$beafTag.'><'.$beafTag.' class="beafter"></'.$beafTag.'></'.$tagName.'>';
      }
    }
    
    return '</'.$tagName.'>';
  }

  public static function £($tagName, $opt = array(), $content = false, $openAndClose = true)
  {
    if (!($tagName = trim($tagName)))
    {
      return '';
    }

    $tagOpt = array();

    // séparation du nom du tag et des attributs dans $tagName
    if ($firstSpacePos = strpos($tagName, ' '))
    {
      $tagNameOpt = substr($tagName, $firstSpacePos + 1);
      $tagName = substr($tagName, 0, $firstSpacePos);

      // DMS STYLE - string opt in name
      dmString::retrieveOptFromString($tagNameOpt, $tagOpt);
    }

    // JQUERY STYLE - css expression
    dmString::retrieveCssFromString($tagName, $tagOpt);

    // ARRAY STYLE - array opt
    if (is_array($opt) && !empty($opt))
    {
      if (isset($opt['json']))
      {
        $tagOpt['class'][] = json_encode($opt['json']);
        unset($opt['json']);
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
        if ($tagName === 'span')
        {
          $content = '&nbsp;';
        }
        else
        {
          $content = null;
        }
      }
    }

    $class = isset($tagOpt['class']) ? $tagOpt['class'] : array();

    if (in_array('beafh', $class) || in_array('beafv', $class))
    {
      $isBeaf = true;
      $tagOpt['class'][] = 'clearfix';
      $beafTag = in_array($tagName, array('span', 'a', 'p')) ? 'span' : 'div';
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
        $tag = '<'.$tagName.$optHtml.'><'.$beafTag.' class="beafore"></'.$beafTag.'><'.$beafTag.' class="beafin">'.$content.'</'.$beafTag.'><'.$beafTag.' class="beafter"></'.$beafTag.'></'.$tagName.'>';
      }
      else
      {
        $tag = '<'.$tagName.$optHtml.'>'.$content.'</'.$tagName.'>';
      }
    }
    else
    {
      if ($isBeaf)
      {
        $tag = '<'.$tagName.$optHtml.'><'.$beafTag.' class="beafore"></'.$beafTag.'><'.$beafTag.' class="beafin">';
      }
      else
      {
        $tag = '<'.$tagName.$optHtml.'>';
      }
    }

    return $tag;
  }
}