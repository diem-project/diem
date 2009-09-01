<?php

class DmMediaForRecordForm extends DmMediaForm
{

	public static function factory(myDoctrineRecord $record, $local, $alias, $required)
	{
		/*
		 * Check first is local column has a value
		 * not to modify the record
		 */
		if ($record->get($local))
		{
			$media = $record->get($alias);
		}
		else
		{
			$media = new DmMedia;
			$media->set('Folder', $record->getDmMediaFolder());
		}

		$form = new self($media);
		$form->configureRequired($required);
		return $form;
	}

	public function configureRequired($required)
	{
		$this->getValidator('file')->setOption('required', $required && $this->getValidator('file')->getOption('required'));

		/*
		 * Add checkbox to remove Media
		 */
		if(!$required && $this->object->exists() && !isset($this->widgetSchema['remove']))
		{
			$this->widgetSchema['remove'] = new sfWidgetFormInputCheckbox;
			$this->validatorSchema['remove'] = new sfValidatorBoolean;
		}
		elseif(isset($this->widgetSchema['remove']))
		{
			unset($this->widgetSchema['remove'], $this->validatorSchema['remove']);
		}
	}

}