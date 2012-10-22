<?php

/*
 * Manage windows to be displayed on admin homepage.
 *
 * Allow plugins and project to filter and modify the windows
 * by listening the dm.admin_homepage.filter_windows event:
 * ---------------------------------------------------------------------
 * $this->dispatcher->connect('dm.admin_homepage.filter_windows', array($this, 'listenToFilterWindowsEvent'));
 * ---------------------------------------------------------------------
 * public function listenToFilterWindowsEvent(sfEvent $event, array $windows)
 * {
 *   // add a myWindow in second column
 *   $windows[1]['myWindow'] = array($this, 'renderMyWindow');
 *
 *   // change the existing weekChart renderer
 *   $windows[0]['weekChart'] = array($this, 'renderWeekChart');
 *
 *   return $windows;
 * }
 * ---------------------------------------------------------------------
 * public function renderMyWindow(dmHelper $helper)
 * {
 *   // render the window with the $helper
 * }
 * ---------------------------------------------------------------------
 */
class dmAdminHomepageManager
{
	/**
	 * @var myUser
	 */
	protected $user;

	/**
	 * @var sfServiceContainer
	 */
	protected $serviceContainer;

	/**
	 * @var sfEventDispatcher
	 */
	protected $dispatcher;

	/**
	 * @var dmHelper
	 */
	protected $helper;

	/**
	 * @var array
	 */
	protected $windows;

	public function __construct(myUser $user, sfServiceContainer $serviceContainer, sfEventDispatcher $dispatcher, dmHelper $helper)
	{
		$this->user							= $user;
		$this->serviceContainer = $serviceContainer;
		$this->dispatcher 			= $dispatcher;
		$this->helper     			= $helper;
	}

	protected function getDefaultWindows()
	{
		// foreach column, declare the windows with needed properties
		return array(
		array(
        'weekChart'     => array('module' => 'dmChart', 'component' => 'little', 'params' => array('name' => 'week'), 'options_param' => 'week_chart.options'),
        'contentChart'  => array('module' => 'dmChart', 'component' => 'little', 'params' => array('name' => 'content'), 'options_param' => 'content_chart.options'),
        'browserChart'  => array('module' => 'dmChart', 'component' => 'little', 'params' => array('name' => 'browser'), 'options_param' => 'browser_chart.options'),
		),
		array(
        'visitChart'    => array('module' => 'dmChart', 'component' => 'little', 'params' => array('name' => 'visit'), 'options_param' => 'visit_chart.options'),
        'logChart'      => array('module' => 'dmChart', 'component' => 'little', 'params' => array('name' => 'log'), 'options_param' => 'log_chart.options'),
		),
		array(
        'requestLog'    => array('module' => 'dmLog', 'component' => 'little', 'params' => array('name' => 'request'), 'options_param' => 'request_log.options'),
        'eventLog'      => array('module' => 'dmLog', 'component' => 'little', 'params' => array('name' => 'event'), 'options_param' => 'event_log.options'),
		)
		);
	}

	protected function getWindows()
	{
		return $this->dispatcher->filter(
		new sfEvent($this, 'dm.admin_homepage.filter_windows'),
		$this->getDefaultWindows()
		)->getReturnValue();
	}

	public function render()
	{
		$html = $this->helper->open('div.dm_admin_homepage_columns.clearfix');

		foreach($this->getWindows() as $column => $windows)
		{
			$html .= $this->renderColumn($windows);
		}

		return $html.$this->helper->close('div');
	}

	protected function renderColumn(array $windows)
	{
		$html = $this->helper->open('div.dm_admin_homepage_column');

		foreach($windows as $window)
		{
			$html .= $this->renderWindow($window);
		}

		return $html.$this->helper->close('div');
	}

	protected function renderWindow($window)
	{
		if(is_callable($window))
		{
			return call_user_func_array($window, array($this->helper));
		}
		elseif(is_array($window))
		{
			$options = isset($window['options_param']) ? $this->serviceContainer->getParameter($window['options_param']) : array();
			if(isset($options['callback']))
			{
				$window['options'] = $options;
				return call_user_func_array($window, array($this->helper));
			}else{
		  	$can = true;
		  	if(isset($options['credentials']))
		  	{
		  		$can = $this->user->can($options['credentials']);
		  	}
		  	return $can ? $this->helper->renderComponent($window['module'], $window['component'], array_merge(array('options' => $options), $window['params'])) : '';
			}
		}
		return '';
	}
}