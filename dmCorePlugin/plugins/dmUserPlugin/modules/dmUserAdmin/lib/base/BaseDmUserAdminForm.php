<?php

class BaseDmUserAdminForm extends BaseDmUserForm
{

	/**
	 * @see sfForm
	 */
	public function setup()
	{
		parent::setup();

		unset(
		$this['last_login'],
		$this['created_at'],
		$this['updated_at'],
		$this['salt'],
		$this['algorithm'],
		$this['forgot_password_code']
		);

		if (isset($this->widgetSchema['groups_list']))
		{
			$this->widgetSchema['groups_list']->setLabel('Groups');
		}
		if (isset($this->widgetSchema['permissions_list']))
		{
			$this->widgetSchema['permissions_list']->setLabel('Permissions');
		}

		if($this->needsWidget('password'))
		{
			$this->widgetSchema['password'] = new sfWidgetFormInputPassword();
			$this->validatorSchema['password']->setOption('required', $this->object->isNew());
			$this->widgetSchema['password_again'] = new sfWidgetFormInputPassword();
			$this->validatorSchema['password_again'] = clone $this->validatorSchema['password'];

			$this->widgetSchema->moveField('password_again', 'after', 'password');

			$this->validatorSchema['username'] = new sfValidatorAnd(array(
			$this->validatorSchema['username'],
			new sfValidatorRegex(array('pattern' => '/^[\w\d\-\s@\.]+$/')),
			));
			$this->mergePostValidator(new sfValidatorSchemaCompare('password', sfValidatorSchemaCompare::EQUAL, 'password_again', array(), array('invalid' => 'The two passwords must be the same.')));
		}

		if($this->needsWidget('email'))
		{
			$this->changeToEmail('email');
		}
	}
}