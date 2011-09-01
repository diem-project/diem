<?php

class dmFrontWebController extends sfFrontWebController
{
  /**
   * @see sfFrontWebController
   */
  public function redirect($url, $delay = 0, $statusCode = 302)
  {
    $this->dispatcher->notify(new sfEvent($this, 'dm.controller.redirect'));

    return parent::redirect($url, $delay, $statusCode);
  }

  /**
   * Dispatches a request.
   *
   * This will determine which module and action to use by request parameters specified by the user.
   */
  public function dispatch()
  {
    try
    {
      // reinitialize filters (needed for unit and functional tests)
      sfFilter::$filterCalled = array();

      // determine our module and action
      $request    = $this->context->getRequest();
      $moduleName = $request->getParameter('module');
      $actionName = $request->getParameter('action');

      if (empty($moduleName) || empty($actionName))
      {
        throw new sfError404Exception(sprintf('Empty module and/or action after parsing the URL "%s" (%s/%s).', $request->getPathInfo(), $moduleName, $actionName));
      }

      // make the first request
      $this->forward($moduleName, $actionName);
    }
    catch (sfError404Exception $e)
    {
      if (!sfConfig::get('sf_web_debug')) {
      	$this->forward('dmFront', 'error404');
      } else {
        $e->printStackTrace();
      }
    }
    catch (sfException $e)
    {
      $e->printStackTrace();
    }
    catch (Exception $e)
    {
      sfException::createFromException($e)->printStackTrace();
    }
  }
}