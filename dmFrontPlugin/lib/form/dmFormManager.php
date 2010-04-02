<?php

class dmFormManager implements ArrayAccess
{
  protected
  $serviceContainer,
  $dispatcher,
  $forms;
  
  public function __construct(dmFrontServiceContainer $serviceContainer, sfEventDispatcher $dispatcher)
  {
    $this->serviceContainer = $serviceContainer;
    $this->dispatcher       = $dispatcher;

    $this->initialize();
  }
  
  public function initialize()
  {
    $this->forms = array();
  }
  
  protected function createForm($name)
  {
    $formClass = $name.'Form';
      
    if (!class_exists($formClass = $name.'Form') && !class_exists($formClass = $name))
    {
      throw new InvalidArgumentException(sprintf('The form manager has no "%s" form.', $formClass));
    }
    
    $form = new $formClass;
    
    $this->prepareFormForPage($form);
    
    return $form;
  }
  
  protected function prepareFormForPage(dmForm $form)
  {
    if (!$page = $this->serviceContainer->getParameter('context.page'))
    {
      return $form;
    }
    
    if (!$pageRecord = $page->getRecord())
    {
      return $form;
    }
    
    foreach($form->getWidgetSchema()->getFields() as $widgetKey => $widget)
    {
      $widgetModel = $widget->getOption('model');
      
      if ($widget instanceof sfWidgetFormDoctrineChoice && $pageRecord instanceof $widgetModel)
      {
        $form->changeToHidden($widgetKey)->setDefault($widgetKey, $pageRecord->getPrimaryKey());
      }
    }
  }
  
  /**
   * Returns true if the parameter exists (implements the ArrayAccess interface).
   *
   * @param  string  $name  The parameter name
   *
   * @return Boolean true if the parameter exists, false otherwise
   */
  public function offsetExists($name)
  {
    return array_key_exists($name, $this->forms);
  }

  /**
   * Returns a parameter value (implements the ArrayAccess interface).
   *
   * @param  string  $name  The parameter name
   *
   * @return mixed  The parameter value
   */
  public function offsetGet($name)
  {
    $name = dmString::camelize($name);

    if(!isset($this->forms[$name]))
    {
      throw new dmFormNotFoundException('The form '.$name.' is not loaded');
    }

    return $this->forms[$name];
  }

  /**
   * Sets a parameter (implements the ArrayAccess interface).
   *
   * @param string  $name   The parameter name
   * @param mixed   $value  The parameter value 
   */
  public function offsetSet($name, $value)
  {
    $name = dmString::camelize($name);
    
    if (!$value instanceof dmForm)
    {
      throw new InvalidArgumentException(sprintf('The object "%s" is not an instance of dmForm', get_class($value)));
    }

    $this->prepareFormForPage($value);
    
    $this->forms[$name] = $value;
  }

  /**
   * Removes a parameter (implements the ArrayAccess interface).
   *
   * @param string $name    The parameter name
   */
  public function offsetUnset($name)
  {
    unset($this->forms[$name]);
  }
}