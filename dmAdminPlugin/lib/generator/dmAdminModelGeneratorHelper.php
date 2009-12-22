<?php

abstract class dmAdminModelGeneratorHelper extends sfModelGeneratorHelper
{
  protected
  $module;
  
  public function __construct(dmModule $module)
  {
    $this->module = $module;
  }

  protected function getModule()
  {
    return $this->module;
  }

  public function linkToViewPage($object, $params)
  {
    try
    {
      $page = $object->getDmPage();
    }
    catch(Exception $e)
    {
      if (sfConfig::get('dm_debug'))
      {
        throw $e;
      }
      
      return '';
    }
    
    if (!$page)
    {
      return '';
    }
  
    return
    '<li class="sf_admin_action_view_page">'.
    £link('app:front/'.$page->get('slug'))
    ->title(__($params['title'], array('%1%' => strtolower($this->getModule()->getName()))))
    ->text(__($params['label']))
    ->set('.s16.s16_file_html.sf_admin_action')
    ->target('blank').
    '</li>';
  }
  


  public function linkToNew($params)
  {
    return link_to1(
    __($params['label']), $this->getRouteArrayForAction('new'),
    array(
      'class' => 'sf_admin_action_new sf_admin_action s16 s16_add',
      'title' => __($params['title'], array('%1%' => strtolower($this->getModule()->getName())))
    ));
  }

  public function linkToDelete($object, $params)
  {
    $title = __($params['title'], array('%1%' => strtolower($this->getModule()->getName())));
    return '<li class="sf_admin_action_delete">'.link_to1(__($params['label']), $this->getRouteArrayForAction('delete', $object),
    array(
    'class' => 's16 s16_delete dm_delete_link sf_admin_action',
    'title' => $title,
    'method' => 'delete',
    'confirm' => $title.' ?'
    )).'</li>';
  }

  public function linkToList($params)
  {
    return '<li class="sf_admin_action_list">'.link_to1(__($params['label']), $this->getRouteArrayForAction('list'), array('class' => 's16 s16_arrow_left')).'</li>';
  }

  public function linkToSave($object, $params)
  {
    return '<li class="sf_admin_action_save"><input class="green" type="submit" value="'.__($params['label']).'" /></li>';
  }

  public function linkToAdd($params)
  {
    return '<li class="sf_admin_action_add">'.$this->linkToNew($params).'</li>';
  }

  public function linkToSaveAndAdd($object, $params)
  {
    return '<li class="sf_admin_action_save_and_add"><input class="green" type="submit" value="'.__($params['label']).'" name="_save_and_add" /></li>';
  }

  public function linkToSaveAndList($object, $params)
  {
    return '<li class="sf_admin_action_save_and_list"><input class="green" type="submit" value="'.__($params['label']).'" name="_save_and_list" /></li>';
  }

  public function linkToSaveAndNext($object, $params)
  {
    return '<li class="sf_admin_action_save_and_next"><input class="green" type="submit" value="'.__($params['label']).'" name="_save_and_next" /></li>';
  }
  
  public function linkToHistory($object, $params)
  {
    if (!$object->getTable()->isVersionable())
    {
      return '';
    }
    
    return '<li class="sf_admin_action_history">'.
    link_to1(
      __($params['label']), $this->getRouteArrayForAction('history', $object),
      array(
        'class' => 'sf_admin_action s16 s16_clock_history',
        'title' => __($params['title'], array('%1%' => strtolower($this->getModule()->getName())))
      )
    ).
    '</li>';
  }
}