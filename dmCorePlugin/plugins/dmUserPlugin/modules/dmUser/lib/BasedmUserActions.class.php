<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 *
 * @package    symfony
 * @subpackage plugin
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: BasedmUserActions.class.php 23319 2009-10-25 12:22:23Z Kris.Wallsmith $
 */
class BasedmUserActions extends autodmUserActions
{
  public function validateEdit()
  {
    if ($this->getRequest()->getMethod() == sfRequest::POST && !$this->getRequestParameter('id'))
    {
      if ($this->getRequestParameter('dm_user[password]') == '')
      {
        $this->getRequest()->setError('dm_user{password}', $this->getContext()->getI18N()->__('Password is mandatory'));

        return false;
      }
    }

    return true;
  }
}
