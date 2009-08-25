<?php

class dmFrontWebController extends sfFrontWebController
{
	/**
	 * Redirects the request to another URL.
	 *
	 * @param string $url        An existing URL
	 * @param int    $delay      A delay in seconds before redirecting. This is only needed on
	 *                           browsers that do not support HTTP headers
	 * @param int    $statusCode The status code
	 */
	public function redirect($url, $delay = 0, $statusCode = 302)
	{
		$this->dispatcher->notify(new sfEvent($this, 'dm.controller.redirect', array($url)));
		
		parent::redirect($url, $delay, $statusCode);
	}
}