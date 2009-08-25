<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Processes the "remember me" cookie.
 *
 * This filter should be added to the application filters.yml file **above**
 * the security filter:
 *
 *    remember_me:
 *      class: sfGuardRememberMeFilter
 *
 *    security: ~
 *
 * @package    symfony
 * @subpackage plugin
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfGuardRememberMeFilter.class.php 16119 2009-03-09 17:03:06Z Kris.Wallsmith $
 */
class sfGuardRememberMeFilter extends sfFilter
{
  /**
   * @see sfFilter
   */
  public function execute($filterChain)
  {
    $cookieName = sfConfig::get('app_sf_guard_plugin_remember_cookie_name', 'sfRemember');

    if (
      $this->isFirstCall()
      &&
      $this->context->getUser()->isAnonymous()
      &&
      $cookie = $this->context->getRequest()->getCookie($cookieName)
    )
    {
      $q = dmDb::query('sfGuardRememberKey r')
            ->innerJoin('r.sfGuardUser u')
            ->where('r.remember_key = ?', $cookie);

      if ($q->count())
      {
        $this->context->getUser()->signIn($q->fetchOne()->sfGuardUser);
      }
    }

    $filterChain->execute();
  }
}
