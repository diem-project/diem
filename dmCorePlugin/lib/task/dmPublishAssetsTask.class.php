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

    foreach (array('dmAdminPlugin', 'dmFrontPlugin') as $plugin)
    {
      $this->logSection('plugin', 'Configuring plugin - '.$plugin);
      $this->installPluginAssets($plugin, dm::getDir().'/'.$plugin);
    }

    // remove useless doctrine assets
    if (is_readable($doctrineAssetPath = dmOs::join($projectWebPath, 'sfDoctrinePlugin')))
    {
      if (!is_link($doctrineAssetPath))
      {
        $filesystem->deleteDirContent($doctrineAssetPath);
      }
      
      $filesystem->remove($doctrineAssetPath);
    }

    // remove web cache dir
    $webCacheDir = sfConfig::get('sf_web_dir').'/cache';
    if (is_link($webCacheDir))
    {
      $filesystem->remove($webCacheDir);
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
