<?php

if ($sf_context->isModuleAction('dmAdmin', 'index'))
{
  return;
}

$links = array(£link()->text(£('span.s16block.s16_home_gray', '&nbsp;'))->title(__('Home'))->set('.home'));

if ($module = $sf_context->getModuleManager()->getModuleOrNull($sf_request->getParameter('module')))
{
  $route = $sf_request->getAttribute('sf_route');
  $space = $module->getSpace();
  $type  = $space->getType();
  
  $links[] = £link($sf_context->getRouting()->getModuleTypeUrl($type))->text(__($type->getPublicName()));
  $links[] = £link($sf_context->getRouting()->getModuleSpaceUrl($space))->text(__($space->getPublicName()));

  if ($route instanceof dmDoctrineRoute && $route->isType('object'))
  {
    $links[] = £link('@'.$module->getUnderscore())->text(__($module->getPlural()));
    
    if ($sf_context->getActionName() == 'new')
    {
      $links[] = £('h1', __('New'));
    }
    else
    {
      $object = $sf_request->getAttribute('sf_route')->getObject();
      $links[] = £('h1', $object->__toString());
    }
  }
  elseif(($action = $sf_context->getActionName()) !== 'index')
  {
    $links[] = £link('@'.$module->getUnderscore())->text(__($module->getPlural()));
    $links[] = £('h1', __($action));
  }
  else
  {
    $links[] = £('h1', __($module->getPlural()));
  }
}

echo £o('div#breadCrumb.mt10.clearfix');

echo £('ol', '<li>'.implode('</li><li class="sep">&gt;</li><li>', $links).'</li>'.get_slot('dm.breadCrumb'));

if (has_slot('dm.mini_search_form'))
{
  echo £('div.dm_mini_search_form', get_slot('dm.mini_search_form'));
}

echo £c('div');