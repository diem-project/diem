<?php

class dmPropelPager extends sfPropelFinderPager
{

	public function getNavigation($opt = null)
	{
		$opt = dmString::toArray($opt);

    return 'navigation';
	}

}