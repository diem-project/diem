<?php

if ($sf_context->isModuleAction('dmAdmin', 'index'))
{
  return;
}

$links = array(£link()->text(£('span.s16block.s16_home_gray', __('Home')))->set('.home'));

if ($module = $sf_context->getModuleManager()->getModuleOrNull($sf_request->getParameter('module')))
{
  $route = $sf_request->getAttribute('sf_route');
  $space = $module->getSpace();
  $type  = $space->getType();
  
  $links[] = £link($sf_context->getRouting()->getModuleTypeUrl($type))->text(£('span', __($type->getPublicName())));
  $links[] = £link($sf_context->getRouting()->getModuleSpaceUrl($space))->text(£('span', __($space->getPublicName())));

  if ($route instanceof dmDoctrineRoute && $route->isType('object'))
  {
    $links[] = £link('@'.$module->getUnderscore())->text(__($module->getPlural()));
    
    if ($sf_context->getActionName() == 'new')
    {
      $links[] = £('span.link', __('New'));
    }
    else
    {
      $object = $sf_request->getAttribute('sf_route')->getObject();
      $links[] = £('span.link', $object->__toString());
    }
  }
  elseif(($action = $sf_context->getActionName()) !== 'index')
  {
    $links[] = £link('@'.$module->getUnderscore())->text(__($module->getPlural()));
    $links[] = £('span.link', __($action));
  }
  else
  {
    $links[] = £('span.link', __($module->getPlural()));
  }
}

echo £o("div#breadCrumb.mt10");

echo £o('ol');

foreach($links as $link)
{
  echo '<li>'.$link.'</li>';
}

include_slot('dm.breadCrumb');

echo £c('ol');

echo £c('div');