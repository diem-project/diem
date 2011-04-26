<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Promotes a user as a super administrator.
 *
 * @package    symfony
 * @subpackage task
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Hugo Hamon <hugo.hamon@sensio.com>
 * @version    SVN: $Id: sfGuardPromoteSuperAdminTask.class.php 23319 2009-10-25 12:22:23Z Kris.Wallsmith $
 */
class dmUserPromoteSuperAdminTask extends sfBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('username', sfCommandArgument::REQUIRED, 'The user name'),
    ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_OPTIONAL, 'The application name', null),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
    ));

    $this->namespace = 'dmUser';
    $this->name = 'promote';
    $this->briefDescription = 'Promotes a user as a super administrator';

    $this->detailedDescription = <<<EOF
The [guard:promote|INFO] task promotes a user as a super administrator:

  [./symfony guard:promote fabien|INFO]
EOF;
  }

  /**
   * Executes the task.
   *
   * @param array $arguments An array of arguments
   * @param array $options An array of options
   * @throws sfException
   */
  protected function execute($arguments = array(), $options = array())
  {
    $databaseManager = new sfDatabaseManager($this->configuration);

    $user = Doctrine_Core::getTable('DmUser')->retrieveByUsername($arguments['username']);

    if (!$user)
    {
      throw new sfException(sprintf('User identified by "%s" username does not exist or is not active.', $arguments['username']));
    }

    $user->setIsSuperAdmin(true);
    $user->save();

    $this->logSection('guard', sprintf('User identified by "%s" username has been promoted as super administrator', $arguments['username']));
  }
}