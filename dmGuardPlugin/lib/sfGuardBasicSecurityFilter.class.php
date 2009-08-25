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
 * @package    symfony
 * @subpackage plugin
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id$
 * 
 * @deprecated Use {@link sfGuardRememberMeFilter} instead
 */
class sfGuardBasicSecurityFilter extends sfBasicSecurityFilter
{
  public function execute($filterChain)
  {
    $cookieName = sfConfig::get('app_sf_guard_plugin_remember_cookie_name', 'sfRemember');

    if ($this->isFirstCall())
    {
      // deprecated notice
      $this->context->getEventDispatcher()->notify(new sfEvent($this, 'application.log', array(sprintf('The filter "%s" is deprecated. Use "sfGuardRememberMeFilter" instead.', __CLASS__), 'priority' => sfLogger::NOTICE)));

      if (
        $this->context->getUser()->isAnonymous()
        &&
        $cookie = $this->context->getRequest()->getCookie($cookieName)
      )
      {
        $q = Doctrine_Query::create()
              ->from('sfGuardRememberKey r')
              ->innerJoin('r.sfGuardUser u')
              ->where('r.remember_key = ?', $cookie);

        if ($q->count())
        {
          $this->context->getUser()->signIn($q->fetchRecord()->sfGuardUser);
        }
      }
    }

    parent::execute($filterChain);
  }
}