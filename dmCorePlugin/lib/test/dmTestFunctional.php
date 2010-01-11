<?php

class dmTestFunctional extends sfTestFunctional
{

  public function checks(array $checks)
  {
    $checks = array_merge($this->getDefaultChecks(), $checks);

    foreach($checks as $check => $expected)
    {
      $method = 'is'.dmString::camelize($check);
      
      $this->$method($expected);
    }
  }

  public function getDefaultChecks()
  {
    return array(
      'code' => 200,
      'module_action' => null,
      'h1' => null
    );
  }

  public function has($selector, $value = null)
  {
    return $this
    ->with('response')
    ->begin()
    ->checkElement($selector, $value)
    ->end();
  }

  public function isCode($code)
  {
    if (!$code)
    {
      return $this;
    }

    return $this
    ->with('response')
    ->begin()
    ->isStatusCode($code)
    ->end();
  }

  public function isModuleAction($moduleAction)
  {
    if (!$moduleAction)
    {
      return $this;
    }
    
    list($module, $action) = explode('/', $moduleAction);

    return $this
    ->with('request')
    ->begin()
    ->isParameter('module', $module)
    ->isParameter('action', $action)
    ->end();
  }

  public function isH1($h1)
  {
    if (!$h1)
    {
      return $this;
    }

    return $this
    ->with('response')
    ->begin()
    ->checkElement('h1', $h1)
    ->end();
  }
}