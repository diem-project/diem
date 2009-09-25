<?php

class dmAdminRouting extends dmPatternRouting
{
  
  public function getModuleTypeUrl(dmModuleType $type)
  {
    return '@dm_module_type?moduleTypeName='.dmString::slugify($type->getPublicName());
  }
  
  public function getModuleSpaceUrl(dmModuleSpace $space)
  {
    return '@dm_module_space?moduleTypeName='.dmString::slugify($space->getType()->getPublicName()).'&moduleSpaceName='.dmString::slugify($space->getPublicName());
  }
  
}