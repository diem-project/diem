<?php

require_once(dirname(__FILE__).'/helper/dmUnitTestHelper.php');
$helper = new dmUnitTestHelper();
$helper->boot('front');

$t = new lime_test();

$wtm = $helper->get('widget_type_manager');

$t->ok($wtm instanceof dmWidgetTypeManager, 'got an instanceof dmWidgetTypeManager');

$widgetTypes = $wtm->getWidgetTypes();

$t->ok(is_array($widgetTypes), 'got an array of widget types');

foreach($widgetTypes as $moduleKey => $actions)
{
  foreach($actions as $actionKey => $widgetType)
  {
    $t->diag('Testing '.$moduleKey.'.'.$actionKey.' widget options, component, form and view');
    
    $fullKey = $moduleKey.ucfirst($actionKey);
    $t->is($widgetType->getFullKey(), $fullKey, 'full key is '.$fullKey);

    try
    {
      $useComponent = $helper->get('controller')->componentExists($moduleKey, $actionKey);
    }
    catch(sfConfigurationException $e)
    {
      $useComponent = false;
    }
    
    $t->is($widgetType->useComponent(), $useComponent, $useComponent ? $fullKey.' uses a component' : $fullKey.' uses no component');
    
    $widget = dmDb::create('DmWidget', array(
      'module' => $widgetType->getModule(),
      'action' => $widgetType->getAction(),
      'value'  => '[]'
    ));
    
    $formClass = $widgetType->getOption('form_class');
    try
    {
      $form = new $formClass($widget);
      
      $html = $form->render();
      $t->like($html, '_</form>$_', 'Successfully obtained and rendered a '.$formClass.' instance');
    }
    catch(Exception $e)
    {
      $t->fail('Successfully obtained and rendered a '.$formClass.' instance : '.$e->getMessage());
    }
    
    $viewClass = $widgetType->getOption('view_class');
    try
    {
      $view = new $viewClass($helper->get('context'), $widgetType, $widget->toArrayWithMappedValue());
      
      sfConfig::set('sf_debug', false);
      $html = $view->render();
      sfConfig::set('sf_debug', true);
      
      $t->pass('Successfully obtained and rendered a '.$viewClass.' instance');
    }
    catch(dmFormNotFoundException $e)
    {
      $t->skip('Form not found: aborting test');
    }
    catch(Exception $e)
    {
      $t->fail('Successfully obtained and rendered a '.$viewClass.' instance : '.$e->getMessage());
    }
  }
}