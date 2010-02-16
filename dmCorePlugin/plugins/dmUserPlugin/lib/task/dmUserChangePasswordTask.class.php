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
 * @author     Hugo Hamon <hugo.hamon@sensio.com>
 * @version    SVN: $Id: sfGuardChangePasswordTask.class.php 23319 2009-10-25 12:22:23Z Kris.Wallsmith $
 */
class dmUserChangePasswordTask extends sfBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('username', sfCommandArgument::REQUIRED, 'The user name'),
      new sfCommandArgument('password', sfCommandArgument::REQUIRED, 'The new password'),
    ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_OPTIONAL, 'The application name', null),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
    ));

    $this->namespace = 'dmUser';
    $this->name = 'change-password';
    $this->briefDescription = 'Changes the password of the user';

    $this->detailedDescription = <<<EOF
The [guard:change-password|INFO] task allows to change a user's password:

  [./symfony guard:change-password fabien changeme|INFO]
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

    $user = Doctrine_Core::getTable('DmUser')->findOneByUsername($arguments['username']);

    if (!$user)
    {
      throw new sfException(sprintf('User identified by "%s" username does not exist or is not active.', $arguments['username']));
    }

    $user->setPassword($arguments['password']);
    $user->save();

    $this->logSection('dmUser', sprintf('Password of user identified by "%s" has been changed', $arguments['username']));
  }
}