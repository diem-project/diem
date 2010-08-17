<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Fixes symfony directory permissions.
 *
 * @package    symfony
 * @subpackage task
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfProjectPermissionsTask.class.php 22820 2009-10-06 12:09:34Z Kris.Wallsmith $
 */
class dmProjectPermissionsTask extends sfProjectPermissionsTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    parent::configure();
    
    $this->aliases = array();
    $this->namespace = 'dm';
    $this->name = 'permissions';
    $this->briefDescription = 'Fixes diem directory permissions';

    $this->detailedDescription = <<<EOF
The [project:permissions|INFO] task fixes directory permissions:

  [./symfony project:permissions|INFO]
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $this->logSection('diem', 'Check file permissions');
    parent::execute();
    
    $this->current = null;
    $this->failed  = array();
    
    $dirs = array(
      sfConfig::get('sf_apps_dir'),
      sfConfig::get('sf_data_dir')
    );

    $dirFinder = sfFinder::type('dir');
    $fileFinder = sfFinder::type('file');

    foreach ($dirs as $dir)
    {
      $this->chmod($dirFinder->in($dir), 0777);
      $this->chmod($fileFinder->in($dir), 0666);
    }

    // note those files that failed
    if (count($this->failed))
    {
      $this->logBlock(array_merge(
        array('Permissions on the following file(s) could not be fixed:', ''),
        array_map(create_function('$f', 'return \' - \'.sfDebug::shortenFilePath($f);'), $this->failed)
      ), 'ERROR_LARGE');
    }
  }
  
  /**
   * Returns the filesystem instance.
   *
   * @return sfFilesystem A sfFilesystem instance
   */
  public function getFilesystem()
  {
    if (!isset($this->filesystem))
    {
      $this->filesystem = new sfFilesystem();
    }

    return $this->filesystem;
  }

  /**
   * Captures those chmod commands that fail.
   * 
   * @see http://www.php.net/set_error_handler
   */
  public function handleError($no, $string, $file, $line, $context)
  {
    if (!is_writable($this->current))
    {
      $this->failed[] = $this->current;
    }
  }
}
