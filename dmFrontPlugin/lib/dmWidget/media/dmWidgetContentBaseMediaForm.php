<?php

/*
 * Generic class to handle media based widgets
 */
abstract class dmWidgetContentBaseMediaForm extends dmWidgetPluginForm
{
  public function configure()
  {
    $this->configureMediaFields();

    $this->widgetSchema['width'] = new sfWidgetFormInputText(array(), array('size' => 5));
    $this->validatorSchema['width'] = new dmValidatorCssSize(array(
      'required' => false
    ));

    $this->widgetSchema['height'] = new sfWidgetFormInputText(array(), array('size' => 5));
    $this->validatorSchema['height'] = new dmValidatorCssSize(array(
      'required' => false
    ));
    
    // this input is created with javascript
    $this->validatorSchema['widget_width'] = new sfValidatorInteger(array('required' => false));

    $this->validatorSchema->setPostValidator(
      new sfValidatorCallback(array('callback' => array($this, 'checkMediaSource')))
    );

    parent::configure();
  }
  
  protected function configureMediaFields()
  {
    if($mediaId = $this->getValueOrDefault('mediaId'))
    {
      $media = dmDb::table('DmMedia')->findOneByIdWithFolder($mediaId);
    }
    else
    {
      $media = null;
    }

    $this->widgetSchema['mediaName'] = new sfWidgetFormInputText(array(), array(
      'readonly'  => true,
      'class'     => 'dm_media_receiver'
    ));
    $this->validatorSchema['mediaName'] = new sfValidatorPass();

    $this->widgetSchema->setLabel('mediaName', 'Use media');
    
    if ($media)
    {
      $this->setDefault('mediaName', $media->getRelPath());
    }
    else
    {
      $this->setDefault('mediaName', $this->__('Drag & Drop a media here'));
    }

    $this->widgetSchema['mediaId'] = new sfWidgetFormInputHidden(array());
    $this->validatorSchema['mediaId'] = new sfValidatorDoctrineChoice(array(
      'model'    => 'DmMedia',
      'required' => false
    ));

    $this->widgetSchema['file'] = new sfWidgetFormDmInputFile();
    $this->validatorSchema['file'] = new sfValidatorFile(array(
      'required' => false
    ));
    $this->widgetSchema->setLabel('file', 'Or upload a file');
  }

  public function checkMediaSource($validator, $values)
  {
    if (!$values['mediaId'] && !$values['file'])
    {
      throw new sfValidatorError($validator, 'You must use a media or upload a file');
    }

    return $values;
  }

  public function getWidgetValues()
  {
    $values = parent::getWidgetValues();

    if ($values['file'])
    {
      $this->createMediaFromUploadedFile($values);
    }

    unset($values['mediaName'], $values['file']);

    return $values;
  }
  
  protected function createMediaFromUploadedFile(array &$values)
  {
    $file   = $values['file'];
    $folder = dmDb::table('DmMediaFolder')->findOneByRelPathOrCreate('widget');

    $media = dmDb::table('DmMedia')->findOneByFileAndDmMediaFolderId(
      dmOs::sanitizeFileName($file->getOriginalName()),
      $folder->id
    );

    if (!$media)
    {
      $media = dmDb::create('DmMedia', array(
        'dm_media_folder_id' => $folder->id
      ))
      ->create($file)
      ->saveGet();
    }

    $values['mediaId'] = $media->id;
  }
}