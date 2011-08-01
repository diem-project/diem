<?php
class dmHttpErrorException extends dmException
{
	protected $httpCode, $params;
	
	public function __construct($msg, $httpCode = 404, $params = array())
	{
		$this->httpCode = $httpCode;
		$this->params = $params;
		
		parent::__construct($msg);
	}
	
	/**
	 * Forwards to the 404 action.
	 */
	public function printStackTrace()
	{
		$exception = null === $this->wrappedException ? $this : $this->wrappedException;

		$response = sfContext::getInstance()->getResponse();
		
		if (sfConfig::get('sf_debug'))
		{
			if (null === $response)
			{
				$response = new sfWebResponse(sfContext::getInstance()->getEventDispatcher());
				sfContext::getInstance()->setResponse($response);
			}

			$response->setStatusCode($this->httpCode);

			return parent::printStackTrace();
		}
		else
		{
			// log all exceptions in php log
			if (!sfConfig::get('sf_test'))
			{
				error_log($this->getMessage());
			}

			if(isset($this->params['module']) && isset($this->params['action']))
			{
				sfContext::getInstance()->getController()->forward($this->params['module'], $this->params['action']);
			}
			elseif(sfContext::getInstance()->getController()->actionExists('httpErrors', 'http' . $code))
			{
				sfContext::getInstance()->getController()->forward('httpErrors', 'http' . $code);
			}
			else
			{
				$module = sfConfig::get(sprintf('sf_error_%s_module', $this->httpCode));
				$action = sfConfig::get(sprintf('sf_error_%s_action', $this->httpCode));
				if($module && $action)
				{
					sfContext::getInstance()->getRequest()->setAttribute('http_code', $this->httpCode);
					sfContext::getInstance()->getController()->forward($module, $action);
				}
				else
				{
					$response->setStatusCode($this->httpCode);
				}
			}
		}
	}
}