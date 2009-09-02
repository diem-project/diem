<?php

/**
 * PluginDmMedia form.
 *
 * @package    form
 * @subpackage DmMedia
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
abstract class PluginDmMediaForm extends BaseDmMediaForm
{

	public function setup()
	{
		parent::setup();

		$this->useFields(array('dm_media_folder_id', 'file', 'legend', 'author', 'license'));

		$this->widgetSchema['file'] = new sfWidgetFormDmInputFile();
		$this->validatorSchema['file'] = new sfValidatorFile(array(
      'required' => $this->getObject()->isNew()
		));

		$this->widgetSchema['dm_media_folder_id'] = new sfWidgetFormInputHidden();

		$this->mergePostValidator(new sfValidatorCallback(array('callback' => array($this, 'clearName'))));
		$this->mergePostValidator(new sfValidatorCallback(array('callback' => array($this, 'checkExistingNameInParent'))));
	}

	protected function doUpdateObject($values)
	{
		if (!$values['file'] instanceof sfValidatedFile)
		{
			unset($values['file']);
		}

		parent::doUpdateObject($values);

		if (isset($values['file']))
		{
			if ($this->object->isNew())
			{
				if (!$this->object->create($values['file']))
				{
					throw new dmException('Can not create file for media', $object);
				}
			}
			else
			{
				if (!$this->object->replaceFile($values['file']))
				{
					throw new dmException('Can not replace file for media', $object);
				}
			}
		}
	}

	public function clearName($validator, $values)
	{
		if (!empty($values['file']))
		{
			$filename = dmOs::sanitizeFileName($values['file']->getOriginalName());
			if(empty($filename))
			{
				$error = new sfValidatorError($validator, 'This is a bad name');

				// throw an error bound to the password field
				throw new sfValidatorErrorSchema($validator, array('file' => $error));
			}
		}

		return $values;
	}

	public function checkExistingNameInParent($validator, $values)
	{
		if (!empty($values['file']))
		{
			if(!$folder = dmDb::table('DmMediaFolder')->find($values['dm_media_folder_id']))
			{
				throw new dmException('media has no folder');
			}

			$filename = dmOs::sanitizeFileName($values['file']->getOriginalName());

			if($folder->hasFile($filename))
			{
				$this->throwFileAlreadyExists($validator, $folder, $filename);
			}

			if(!is_writable($folder->fullPath))
			{
				$error = new sfValidatorError($validator, dmProject::unRootify($folder->fullPath)." is not writable");

				// throw an error bound to the file field
				throw new sfValidatorErrorSchema($validator, array('file' => $error));
			}
		}

		return $values;
	}

	protected function throwFileAlreadyExists($validator, $folder, $filename)
	{
		$error = new sfValidatorError($validator, 'Already exists in this folder');

		// throw an error bound to the file field
		throw new sfValidatorErrorSchema($validator, array('file' => $error));
	}
}