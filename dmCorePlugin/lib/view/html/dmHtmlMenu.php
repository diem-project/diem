<?php

class dmHtmlMenu
{

	protected static
	$defaultOptions = array();

	protected
	$menu,
	$options,
	$controller;

	public function __construct($menu)
	{
		$this->menu = $menu;
	}

	public function render($options = array())
	{
		$this->options = array_merge(self::$defaultOptions, dmString::toArray($options));
		$this->controller = sfContext::getInstance()->getController();

		$html = '<ul class="'.$this->joinClasses(array('level0', $this->getLevelOption(0, 'ul_class'))).'">';
		foreach($this->menu as $elem)
		{
			$html .= $this->node($elem, null, 0);
		}
		$html .= '</ul>';

		return $html;
	}

	protected function node($elem, $class = null, $level)
	{
		if (isset($elem["menu"]))
		{
			$html = '<li class="'.$this->getLevelOption($level, 'li_class').'">';

	    $class = trim((isset($elem['class']) ? $elem['class'] : '').' '.$class);

			if (isset($elem["link"]))
			{
				$html .= '<a class="'.$class.'" href="'.$this->controller->genUrl($elem['link']).'">'.$elem["name"].'</a>';
			}
			else
			{
				$html .= '<a class="'.$class.'">'.$elem["name"].'</a>';
			}

			$html .= '<ul class="'.$this->joinClasses(array('level'.($level+1), $this->getLevelOption($level+1, 'ul_class'))).'">';

			$node_number = 1;
			$node_total = count($elem["menu"]);
			foreach($elem["menu"] as $child)
			{
//				if      ($node_total === 1)             $class = "first last";
//				elseif  ($node_number === 1)            $class = "first";
//				elseif  ($node_number === $node_total)  $class = "last";
//				else                                    $class = "";
        $class = '';

				$html .= $this->node($child, $class, $level+1);
				$node_number++;
			}

			$html .= '</ul></li>';
		}
		else
		{
			$html = $this->leaf($elem, $class);
		}
		return $html;
	}

	protected function leaf($elem, $class)
	{
		if (isset($elem['type']))
		{
			if ($elem['type'] === "separator")
			{
				return '<li class="separator">&nbsp;</li>';
			}
		}

		$class = trim((isset($elem['class']) ? $elem['class'].' ' : '').$class);
	
    if (isset($elem['link']))
    {
    	$html = sprintf('<a%s%s href="%s">%s</a>',
        $class ? ' class="'.$class.'"' : '',
        isset($elem['id']) ? ' id="'.$elem['id'].'"' : '',
    	  $this->controller->genUrl($elem['link']),
    	  $elem['name']
    	);
    }
    elseif (isset($elem['anchor']))
    {
      $html = sprintf('<a%s%s href="#%s">%s</a>',
        $class ? ' class="'.$class.'"' : '',
        isset($elem['id']) ? ' id="'.$elem['id'].'"' : '',
        $elem['anchor'],
        $elem['name']
      );
    }
		else
		{
      $html = sprintf('<span%s%s>%s</span>',
        $class ? ' class="'.$class.'"' : '', 
        isset($elem['id']) ? ' id="'.$elem['id'].'"' : '',
        $elem['name']
      );
		}

		return '<li>'.$html.'</li>';
	}

	protected function getLevelOption($level, $name)
	{
		return isset($this->options['level'.$level.'_'.$name]) ? $this->options['level'.$level.'_'.$name] : null;
	}

	protected function joinClasses($classes)
	{
		foreach($classes as $key => $class)
		{
			if(empty($class))
			{
				unset($classes[$key]);
			}
		}
		return trim(implode(' ', $classes));
	}

}