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

		if (sfConfig::get('sf_debug'))
		{
			$response = sfContext::getInstance()->getResponse();
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
				$response->setStatusCode($this->httpCode);
			}
		}
	}
}