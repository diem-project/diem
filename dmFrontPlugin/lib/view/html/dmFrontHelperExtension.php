<?php

class dmFrontHelperExtension
{
  protected
  $dispatcher,
  $serviceContainer;

  public function __construct(sfEventDispatcher $dispatcher, dmFrontBaseServiceContainer $serviceContainer)
  {
    $this->dispatcher       = $dispatcher;
    $this->serviceContainer = $serviceContainer;
  }

  public function connect()
  {
    $this->dispatcher->connect('dm.helper.method_not_found', array($this, 'listenToHelperMethodNotFoundEvent'));
  }

  public function listenToHelperMethodNotFoundEvent(sfEvent $event)
  {
    if(method_exists($this, $event['method']))
    {
      $event->setReturnValue(call_user_func_array(array($this, $event['method']), $event['arguments']));

      return true;
    }

    return false;
  }

  /**
   * Render a widget
   * ->getWidget('main', 'header') //renders your main/header widget
   * ->getWidget('dmWidgetContent', 'title', array('text' => 'Blah', 'tag' => 'h2')) //renders a Diem title widget
   *
   * @return (string) the HTML produced by the widget
   */
  protected function getWidget($module, $action, array $params = array())
  {
    return $this->serviceContainer->getService('page_helper')->renderWidget(array(
      'module'    => $module,
      'action'    => $action,
      'value'     => json_encode($params),
      'css_class' => dmArray::get($params, 'css_class')
    ), true);
  }
}