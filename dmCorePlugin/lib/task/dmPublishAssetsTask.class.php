<?php

class dmPublishAssetsTask extends sfPluginPublishAssetsTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    parent::configure();

    $this->namespace = 'dm';
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    parent::execute($arguments, $options);
    
    $projectWebPath = sfConfig::get('sf_web_dir');
    $filesystem = new dmFilesystem($this->dispatcher, $this->formatter);

    $filesystem->mkdir($projectWebPath.'/dm');

    foreach(array('core', 'admin', 'front') as $plugin)
    {
      $pluginDir = dmOs::join(dm::getDir(), 'dm'.dmString::camelize($plugin).'Plugin');
      $origin = dmOs::join($pluginDir, 'web');
      $target = dmOs::join($projectWebPath, sfConfig::get('dm_'.$plugin.'_asset', 'dm/'.$plugin));

      if (file_exists($origin))
      {
        $filesystem->relativeSymlink($origin, $target, true);
      }
      
      if (is_readable($dmWrongAssetDir = dmOs::join($projectWebPath, 'dm'.dmString::camelize($plugin).'Plugin')))
      {
        if (is_dir($dmWrongAssetDir))
        {
          $filesystem->deleteDirContent($dmWrongAssetDir);
        }
        
        $filesystem->remove($dmWrongAssetDir);
      }
    }
      
    if (is_readable($doctrineAssetPath = dmOs::join($projectWebPath, 'sfDoctrinePlugin')))
    {
      if (is_dir($doctrineAssetPath))
      {
        $filesystem->deleteDirContent($doctrineAssetPath);
      }
        
      $filesystem->remove($doctrineAssetPath);
    }

    $webCacheDir = sfConfig::get('sf_web_dir').'/cache';
    if (function_exists('is_link') && is_link($webCacheDir))
    {
      unlink($webCacheDir);
    }

    // create web cache dir
    $filesystem->mkdir($webCacheDir);

    if (!file_exists(dmOs::join($projectWebPath, 'sf')))
    {
      $filesystem->relativeSymlink(
        realpath(sfConfig::get('sf_symfony_lib_dir').'/../data/web/sf'),
        dmOs::join($projectWebPath, 'sf'),
        true
      );
    }
  }
}
