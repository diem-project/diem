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
 * @version    SVN: $Id: BasesfGuardAuthActions.class.php 23319 2009-10-25 12:22:23Z Kris.Wallsmith $
 */
class BasedmAuthActions extends dmBaseActions
{
  public function executeSignin(dmWebRequest $request)
  {
    $user = $this->getUser();
    
    if ($user->isAuthenticated())
    {
      return $this->redirect('@homepage');
    }

    $this->setLayout(dmOs::join(sfConfig::get('dm_core_dir'), 'plugins/dmUserPlugin/modules/dmAuth/templates/layout'));

    if($request->getParameter('skip_browser_detection'))
    {
      $this->getService('browser_check')->markAsChecked();
    }
    elseif(!$this->getService('browser_check')->check())
    {
      return 'Browser';
    }

    $class = sfConfig::get('dm_security_signin_form', 'DmFormSignin');
    $this->form = new $class();

    $this->form->changeToHidden('remember')->setDefault('remember', true);

    if ($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter('signin'));
      if ($this->form->isValid())
      {
        $values = $this->form->getValues(); 
        $this->getUser()->signin($values['user'], array_key_exists('remember', $values) ? $values['remember'] : true);

        // always redirect to a URL set in app.yml
        // or to the referer
        // or to the homepage
        if ($this->getUser()->can('admin'))
        {
          $signinUrl = sfConfig::get('dm_security_success_signin_url', $user->getReferer($request->getReferer()));
        }
        else
        {
          try
          {
            $signinUrl = $this->context->get('script_name_resolver')->get('front');
          }
          catch(dmException $e)
          {
            // user can't go in admin, and front script_name can't be found.
          }
        }

        return $this->redirect('' != $signinUrl ? $signinUrl : '@homepage');
      }
    }
    else
    {
      if ($request->isXmlHttpRequest())
      {
        $this->getResponse()->setHeaderOnly(true);
        $this->getResponse()->setStatusCode(401);

        return sfView::NONE;
      }

      // if we have been forwarded, then the referer is the current URL
      // if not, this is the referer of the current request
      $user->setReferer($this->getContext()->getActionStack()->getSize() > 1 ? $request->getUri() : $request->getReferer());

      $module = sfConfig::get('sf_login_module');
      if ($this->getModuleName() != $module)
      {
        return $this->redirect($module.'/'.sfConfig::get('sf_login_action'));
      }

      $this->getResponse()->setStatusCode(401);
    }
  }

  public function executeSignout($request)
  {
    $this->getUser()->signOut();

    $signoutUrl = sfConfig::get('dm_security_success_signout_url', $request->getReferer());

    $this->redirect('' != $signoutUrl ? $signoutUrl : '@homepage');
  }

  public function executeSecure()
  {
    $this->getResponse()->setStatusCode(403);
  }

  public function executePassword()
  {
    throw new sfException('This method is not yet implemented.');
  }

}
