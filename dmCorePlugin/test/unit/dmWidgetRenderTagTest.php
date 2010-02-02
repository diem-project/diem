<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot();

$t = new lime_test();

class sfWidgetFormDmTest extends sfWidgetFormInputText
{

  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    $attributes['class'] = json_encode(array(
      'key' => 'value'
    ));

    return parent::render($name, $value, $attributes, $errors);
  }
}

$widget = new sfWidgetFormDmTest;

//dmDebug::kill($widget->render('test'));