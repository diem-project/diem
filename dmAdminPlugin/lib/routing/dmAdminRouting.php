<?php

class dmAdminRouting extends dmPatternRouting
{
  protected
  $mediaFolderPathBaseUrl;
  
  public function getModuleTypeUrl(dmModuleType $type)
  {
    return '@dm_module_type?moduleTypeName='.dmString::slugify($type->getPublicName());
  }
  
  public function getModuleSpaceUrl(dmModuleSpace $space)
  {
    return '@dm_module_space?moduleTypeName='.dmString::slugify($space->getType()->getPublicName()).'&moduleSpaceName='.dmString::slugify($space->getPublicName());
  }
  
  public function getMediaUrl(dmDoctrineRecord $record)
  {
    if($record instanceof DmMediaFolder)
    {
      if (null === $this->mediaFolderPathBaseUrl)
      {
        $this->mediaFolderPathBaseUrl = $this->generate('dm_media_library_path', array('path' => '__DM_MEDIA_PATH_PLACEHOLDER__'));
      }
      
      return str_replace('__DM_MEDIA_PATH_PLACEHOLDER__', $record->getRelPath(), $this->mediaFolderPathBaseUrl);
    }
    elseif($record instanceof DmMedia)
    {
      return 'dmMediaLibrary/file?media_id='.$record->get('id');
    }
    else
    {
      throw new dmException('Can not generate url for '.$record);
    }
  }
}