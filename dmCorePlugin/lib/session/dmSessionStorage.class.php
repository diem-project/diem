<?php
/*
 * This file is part of the dmCorePlugin package.
 * (c) 2011 Diem project
 *
 *  http://www.diem-project.org
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 *
 * @see sfSessionStorage
 *
 */
class dmSessionStorage extends sfSessionStorage
{

	protected 
	$serviceContainer;

	public function initialize($options = null)
	{
		parent::initialize($options);
		$this->options = $options;
	}

	public function setCookieParams($options = null)
	{
		if(null === $options)
		{
			$options = $this->options;
		}
		
		if(!isset($options['session_cookie_domain']) || $options['session_cookie_domain'] == ''){
			//$options['session_cookie_domain']  = '.'.($this->getServiceContainer()->getService('domain')->getDomain());
		}

		parent::initialize($options);
		
		return $this;
	}

	public function setServiceContainer(dmBaseServiceContainer $serviceContainer)
	{
		$this->serviceContainer = $serviceContainer;
		
		return $this;
	}

	public function getServiceContainer()
	{
		return $this->serviceContainer;
	}
}