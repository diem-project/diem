<?php

if ($sf_context->isModuleAction('dmAdmin', 'index'))
{
  return;
}

$links = array(£link()->text(£('span.s16block.s16_home_gray', __('Home')))->set('.home'));

if ($module = $sf_context->getModuleManager()->getModuleOrNull($sf_request->getParameter('module')))
{
  $space = $module->getSpace();
  $type  = $space->getType();
  
  $links[] = £link($sf_context->getRouting()->getModuleTypeUrl($type))->text(£('span', __($type->getPublicName())));
  $links[] = £link($sf_context->getRouting()->getModuleSpaceUrl($space))->text(£('span', __($space->getPublicName())));

  $links[] = £link('@'.$module->getUnderscore())->text(__($module->getPlural()));

  $route = $sf_request->getAttribute('sf_route');
  if ($route instanceof dmDoctrineRoute && $route->isType('object'))
  {
    if ($sf_context->getActionName() == 'new')
    {
      $links[] = £link('@'.$module->getUnderscore().'_new')->text(__('New'));
    }
    else
    {
      $object = $sf_request->getAttribute('sf_route')->getObject();
      $links[] = £link($route->generate($object))->text($object);
    }
  }
  elseif(($action = $sf_context->getActionName()) !== 'index')
  {
    $links[] = £link($sf_request->getUri())->text(__($action));
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