<?php

class dmFrontTestFunctional extends dmTestFunctional
{
  public function getPage()
  {
    return $this->getContext()->getPage();
  }

  public function isPageModuleAction($pageModuleAction)
  {
    if (!$pageModuleAction)
    {
      return $this;
    }

    list($module, $action) = explode('/', $pageModuleAction);

    $this->test()->ok($this->getPage()->isModuleAction($module, $action), 'Page module/action is '.$pageModuleAction);

    return $this;
  }

  public function editPage()
  {
    return $this
    ->info('Edit the current page: '.$this->getPage())
    ->get('/+/dmPage/edit?dm_cpi='.$this->getPage()->id)
    ->checks(array('module_action' => 'dmPage/edit', 'method' => 'get'));
  }

  public function updatePage(array $data = array())
  {
    foreach($data as $field => $value)
    {
      $this->info($field.' = '.$value);
      $this->setField('dm_page_front_edit_form['.$field.']', $value);
    }

    return $this
    ->info('Save the page')
    ->click('Save')
    ->checks(array('module_action' => 'dmPage/edit', 'method' => 'post'))
    ->testResponseContent('|^http://|', 'like')
    ->get('/'.($this->getPage()->slug ? $this->getPage()->slug : 'index.php'));
  }

  public function editWidget(DmWidget $widget)
  {
    $this
    ->info('Edit the '.$widget->module.'/'.$widget->action.' widget')
    ->get(sprintf('/index.php/+/dmWidget/edit?widget_id=%d&dm_cpi=%d', $widget->id, $this->getPage()->id))
    ->checks(array('module_action' => 'dmWidget/edit', 'method' => 'get'));

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

    $this->info('Add a '.$moduleAction.' widget');

    try
    {
      $this->get(sprintf('/index.php/+/dmWidget/add?to_dm_zone=%d&mod='.$module.'&act='.$action.'&dm_cpi=%d', $zone->id, $this->getPage()->id));

      $this
      ->checks(array('module_action' => 'dmWidget/add', 'method' => 'get'))
      ->has('.dm_widget.'.str_replace('dm_widget_', '', dmString::underscore($module)).'.'.dmString::underscore($action));
    }
    catch(dmFormNotFoundException $e)
    {
    }

    $zone->refreshRelated('Widgets');

    return $this;
  }
}