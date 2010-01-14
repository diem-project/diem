<?php

class dmTestFunctional extends sfTestFunctional
{

  public function checks(array $checks = array())
  {
    $checks = array_merge($this->getDefaultChecks(), $checks);

    foreach($checks as $check => $expected)
    {
      $method = 'is'.dmString::camelize($check);
      
      $this->$method($expected);
    }

    return $this;
  }

  public function getDefaultChecks()
  {
    return array(
      'code' => 200,
      'module_action' => null,
      'h1' => null,
      'method' => null
    );
  }

  public function has($selector, $value = true)
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

  public function isMethod($method)
  {
    if (!$method)
    {
      return $this;
    }

    return $this
    ->with('request')
    ->begin()
    ->isMethod($method)
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

  public function redirect()
  {
    return $this
    ->with('response')->begin()
    ->isRedirected()
    ->end()
    ->followRedirect();
  }

  public function getPage()
  {
    return $this->getContext()->getPage();
  }

  public function editWidget(DmWidget $widget)
  {
    $this
    ->info('Edit the '.$widget->module.'/'.$widget->action.' widget')
    ->get(sprintf('/index.php/+/dmWidget/edit?widget_id=%d&dm_cpi=%d', $widget->id, $this->getPage()->id))
    ->checks(array('module_action' => 'dmWidget/edit', 'method' => 'get'));
    
    $response = $this->getResponse();
    $this->test()->is($response->getContentType(), 'application/json', 'Edit widget return json');
    $this->test()->ok($html = dmArray::get(json_decode($response->getContent(), true), 'html'), 'json contains html');

    $response->setContentType('text/html');
    $response->setContent($html);

    $this->browser->setResponse($response);

    return $this;
  }

  public function updateWidget(DmWidget $widget, array $data = array())
  {
    foreach($data as $field => $value)
    {
      $this->info($field.' = '.$value);
      $this->setField(dmString::underscore($widget->module).'_'.dmString::underscore($widget->action).'_form_'.$widget->id.'['.$field.']', $value);
    }

    return $this
    ->info('Save the '.$widget->module.'/'.$widget->action.' widget')
    ->click('Save and close')
    ->checks(array('module_action' => 'dmWidget/edit', 'method' => 'post'));
  }

  public function addWidget(DmZone $zone, $moduleAction)
  {
    list($module, $action) = explode('/', $moduleAction);
    
    $this
    ->info('Add a '.$moduleAction.' widget')
    ->get(sprintf('/index.php/+/dmWidget/add?to_dm_zone=%d&mod='.$module.'&act='.$action.'&dm_cpi=%d', $zone->id, $this->getPage()->id))
    ->checks(array('module_action' => 'dmWidget/add', 'method' => 'get'))
    ->has('.dm_widget.'.dmString::underscore($module).'.'.dmString::underscore($action));

    $zone->refreshRelated('Widgets');

    return $this;
  }

  public function testResponseContent($content)
  {
    $this->test()->is($content, $this->getResponse()->getContent(), 'response content is valid');

    return $this;
  }
}