<?php

class BasedmServiceActions extends dmBaseActions
{
  protected static
  $availableServices = array(
    'dmSetup',
    'dmSprite',
    'dmData',
    'dmConfig',
    'dmClearCache',
    'dmPageSync',
    'dmRefresh',
    'dmLoremize',
    'dmUpdateSeo'
  );

  public function executeIndex(sfWebRequest $request)
  {
    $this->services = self::$availableServices;
  }

  public function executeLaunch(sfWebRequest $request)
  {
    $this->forward404Unless(
      in_array(
        $serviceName = $request->getParameter('name'),
        self::$availableServices
      ),
      "You must provide a service name parameter"
    );

    $this->iterations = $request->getParameter('nb_iterations', 1);
    
    $options = $request->getParameterHolder()->getAll();
    unset($options['module'], $options['action'], $options['name'], $options['redirect'], $options['nb_iterations']);

    $serviceClass = $serviceName.'Service';

    $service = new $serviceClass($this->dispatcher);
    
    $service->addOptions($options);
    
    $service->setUser($this->getUser());
    
    $this->forwardSecureUnless($this->getUser()->can($service->getCredentials()));
    
    $timer = dmDebug::timer($serviceClass.'::execute');

    for($it = 1; $it <= $this->iterations; $it++)
    {
      $service->execute();
    }

    $this->time = $timer->getElapsedTime();

    if (!$request->hasParameter('redirect') || $request->getParameter('redirect') ==1)
    {
      return $this->redirectBack();
    }

    $this->service = $serviceClass;
    $this->services = self::$availableServices;
  }

}