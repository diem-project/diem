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
    
    $this->changeToHidden('dm_media_folder_id');

    $this->mergePostValidator(new sfValidatorCallback(array('callback' => array($this, 'clearName'))));
    $this->mergePostValidator(new sfValidatorCallback(array('callback' => array($this, 'checkFolder'))));
  }

  protected function doUpdateObject($values)
  {
    if (isset($values['file']) && $values['file'] instanceof sfValidatedFile)
    {
      $validatedFile = $values['file'];
    }
    else
    {
      $validatedFile = null;
    }
    
    unset($values['file']);
    
    parent::doUpdateObject($values);

    if ($validatedFile)
    {
      if ($this->object->isNew())
      {
        if (!$this->object->create($validatedFile))
        {
          throw new dmException(sprintf('Can not create file for media %s', $this->object));
        }
      }
      else
      {
        if (!$this->object->replaceFile($validatedFile))
        {
          throw new dmException(sprintf('Can not replace file for media %s', $object));
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

  public function checkFolder($validator, $values)
  {
    if (!empty($values['file']))
    {
      if(!$folder = dmDb::table('DmMediaFolder')->find($values['dm_media_folder_id']))
      {
        throw new dmException('media has no folder');
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
}