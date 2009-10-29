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
    parent::execute();
    
    $this->chmod(sfConfig::get('sf_apps_dir'), 0777);
    
    $this->chmod(sfConfig::get('sf_lib_dir'), 0777);
    
    $this->chmod(sfConfig::get('sf_data_dir'), 0777);
    
    $this->chmod(sfConfig::get('sf_web_dir'), 0777);
  }
}
