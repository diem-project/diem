<?php

class dmHtmlMenu
{

	protected static
	$defaultOptions = array();

	protected
	$menu,
	$options;

	public function __construct(array $menu)
	{
		$this->menu = $menu;
	}

	public function render($options = array())
	{
		$this->options = array_merge(self::$defaultOptions, dmString::toArray($options));

		$html = '<ul class="'.dmArray::toHtmlCssClasses(array('level0', $this->getLevelOption(0, 'ul_class'))).'">';
		foreach($this->menu as $elem)
		{
			$html .= $this->node($elem, null, 0);
		}
		$html .= '</ul>';

		return $html;
	}

	protected function node($elem, $class = null, $level)
	{
		if (isset($elem['menu']))
		{
		  if($liClass = $this->getLevelOption($level, 'li_class'))
		  {
		    $html = '<li class="'.$liClass.'">';
		  }
		  else
		  {
		    $html = '<li>';
		  }

			if (null !== $class || !empty($elem['class']))
			{
	      $classDeclaration = 'class="'.((isset($elem['class']) ? $elem['class'].' ' : '').$class).'" ';
			}
			else
			{
			  $classDeclaration = '';
			}

			if (isset($elem["link"]))
			{
				$html .= '<a '.$classDeclaration.'href="'.$elem['link'].'">'.$elem['name'].'</a>';
			}
			else
			{
				$html .= '<a '.$classDeclaration.'>'.$elem['name'].'</a>';
			}

			$html .= '<ul class="'.dmArray::toHtmlCssClasses(array('level'.($level+1), $this->getLevelOption($level+1, 'ul_class'))).'">';

			$node_number = 1;
			$node_total = count($elem["menu"]);
			foreach($elem['menu'] as $child)
			{
//				if      ($node_total === 1)             $class = "first last";
//				elseif  ($node_number === 1)            $class = "first";
//				elseif  ($node_number === $node_total)  $class = "last";
//				else                                    $class = null;
        $class = null;

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

	protected function leaf($elem, $class = null)
	{
		if (isset($elem['type']))
		{
			if ($elem['type'] === "separator")
			{
				return '<li class="separator">&nbsp;</li>';
			}
		}

		if (null !== $class || !empty($elem['class']))
		{
		  $classDeclaration = 'class="'.((isset($elem['class']) ? $elem['class'].' ' : '').$class).'" ';
		}
		else
		{
		  $classDeclaration = '';
		}
		
		if(!empty($elem['id']))
		{
		  $idDeclaration = 'id="'.$elem['id'].'" ';
		}
		else
		{
		  $idDeclaration = '';
		}
	
    if (isset($elem['link']))
    {
    	$html = '<a '.$classDeclaration.$idDeclaration.'href="'.$elem['link'].'">'.$elem['name'].'</a>';
    }
    elseif (isset($elem['anchor']))
    {
      $html = '<a '.$classDeclaration.$idDeclaration.'href="#'.$elem['anchor'].'">'.$elem['name'].'</a>';
    }
		else
		{
      $html = '<span '.$classDeclaration.$idDeclaration.'>'.$elem['name'].'</span>';
		}

		return '<li>'.$html.'</li>';
	}

	protected function getLevelOption($level, $name)
	{
		return isset($this->options['level'.$level.'_'.$name]) ? $this->options['level'.$level.'_'.$name] : null;
	}

}