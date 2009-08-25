<?php

class dmAdminHelper
{

	public static function getBreadCrumb()
	{
		$request = dm::getRequest();
		$context = dmContext::getInstance();
		$action  = sfContext::getInstance()->getActionName();

		$links = array(
		  dmAdminLinkTag::build()->name(£('span.s16block.s16_home_gray', dm::getI18n()->__('Home')))->set('.home')
		);

    if ($module = $context->getModule())
    {
      $links[] = self::getLinkToModuleType($module->getSpace()->getType());
      $links[] = self::getLinkToModuleSpace($module->getSpace());

      $links[] = dmAdminLinkTag::build(
        dm::getRouting()->hasRouteName($module->getUnderscore()) ? '@'.$module->getUnderscore() : $module->getKey()
      )->name(dm::getI18n()->__($module->getPlural(), array(), 'admin'));

      if ($context->isFormPage())
      {
      	try
      	{
          $object = $request->getAttribute('sf_route')->getObject();
          $links[] = dmAdminLinkTag::build(array($module->getUnderscore().'_edit', $object))->name($object);
      	}
      	catch(sfError404Exception $e)
      	{
      		$links[] = dmAdminLinkTag::build('@'.$module->getUnderscore().'_new')->name(dm::getI18n()->__('New'));
      	}
      }
      elseif($action !== 'index')
      {
      	$links[] = dmAdminLinkTag::build($request->getUri())->name(dm::getI18n()->__($action));
      }
    }
    elseif($context->isModuleAction('dmAdmin', 'moduleType'))
    {
      $links[] = self::getLinkToModuleType($context->getModuleType());
    }
    elseif($context->isModuleAction('dmAdmin', 'moduleSpace'))
    {
      $links[] = self::getLinkToModuleType($context->getModuleType());
      $links[] = self::getLinkToModuleSpace($context->getModuleSpace());
    }

    return $links;
	}

	public static function getLinkToModuleType(dmModuleType $type)
	{
		return dmAdminLinkTag::build(array('sf_route' => 'dm_module_type', 'moduleTypeName' => $type->getSlug()))->name(£('span', dm::getI18n()->__($type->getPublicName())));
	}

	public static function getLinkToModuleSpace(dmModuleSpace $space)
	{
		return dmAdminLinkTag::build(array('sf_route' => 'dm_module_space', 'moduleTypeName' => $space->getType()->getSlug(), 'moduleSpaceName' => $space->getSlug()))->name(£('span', dm::getI18n()->__($space->getName())));
	}

}