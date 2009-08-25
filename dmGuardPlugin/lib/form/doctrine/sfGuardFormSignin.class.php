<?php

/**
 * sfGuardFormSignin for sfGuardAuth signin action
 *
 * @package form
 * @package sf_guard_user
 */
class sfGuardFormSignin extends BasesfGuardFormSignin
{
	public function configure()
	{
		parent::configure();

		$this->widgetSchema['remember'] = new sfWidgetFormInputHidden();

		$this->setDefault('remember', true);
	}
}