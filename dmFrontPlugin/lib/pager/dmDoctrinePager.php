<?php

abstract class dmDoctrinePager extends sfDoctrinePager
{
	protected
	$navigationConfiguration = array(
	  'top' => true,
	  'bottom' => true
	),
	$navigationCache =array();

	public function configureNavigation(array $navigationConfiguration)
	{
		$this->navigationConfiguration = $navigationConfiguration;
	}

	public function getNavigationTop($options = array())
	{
		if ($this->navigationConfiguration['top'])
		{
			return $this->getNavigation($options);
		}
	}

	public function getNavigationBottom($options = array())
	{
		if ($this->navigationConfiguration['bottom'])
		{
			return $this->getNavigation($options);
		}
	}

	protected function getNavigationDefaults()
	{
		return array(
      'separator'       => null,
      'class'           => null,
      'currentClass'    => 'current',
      'first'           => "&lt;&lt;",
      'prev'            => "&lt;",
      'next'            => "&gt;",
      'last'            => "&gt;&gt;",
      'nbLinks'         => 9,
      'uri'             => dm::getRequest()->getUri()
		);
	}

	public function getNavigation($options = array())
	{
		if (!$this->haveToPaginate())
		{
			return '';
		}

		$options = dmString::toArray($options);

		$hash = md5(var_export($options, true));

		if (isset($this->navigationCache[$hash]))
		{
			return $this->navigationCache[$hash];
		}

		$options = array_merge($this->getNavigationDefaults(), $options);

		$options['uri'] = preg_replace("|/page/([0-9]+)|", "?page=$1", $options['uri']);

		sfContext::getInstance()->getConfiguration()->loadHelpers('Dm');

		$html = £o('div.pager'.(!empty($options['class']) ? '.'.implode('.', $options['class']) : ''));

		$html .= £o('ul.clearfix');

		// First and previous page
		if ($this->getPage() != 1)
		{
			if($options['first'])
			{
				$html .= £("li.page.first", £link($options['uri'])->param('page', $this->getFirstPage())->name($options['first']));
			}

			if($options['prev'])
			{
				$html .= £("li.page.prev", £link($options['uri'])->param('page', $this->getPreviousPage())->name($options['prev']));
			}
		}

		// Pages one by one
		$links = array();
		foreach ($this->getLinks($options['nbLinks']) as $page)
		{
			// current page
			if($page == $this->getPage())
			{
				$links[] = £("li.page.".$options['currentClass'], £('span.link', $page));
			}
			else
			{
				$links[] = £("li.page", £link($options['uri'])->param('page', $page)->name($page));
			}
		}

		$html .= join($options['separator'] ? '<li>'.$separateur.'</li>' : '', $links);

		// Next and last page
		if ($this->getPage() != $this->getCurrentMaxLink())
		{
      if($options['next'])
      {
        $html .= £("li.page.next", £link($options['uri'])->param('page', $this->getNextPage())->name($options['next']));
      }
      if($options['last'])
      {
        $html .= £("li.page.first", £link($options['uri'])->param('page', $this->getLastPage())->name($options['last']));
      }
		}

		$html .= '</ul></div>';

    $html = preg_replace("|\?page=([0-9]+)|", "/page/$1", $html);

		$this->navigationCache[$hash] = $html;

		return $html;
	}

}