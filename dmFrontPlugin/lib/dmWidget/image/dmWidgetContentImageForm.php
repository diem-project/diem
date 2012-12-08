<?php

class dmWidgetContentImageForm extends dmWidgetContentBaseMediaForm
{
  
  protected $target = array(
      '_self' => 'Same window',
      '_blank' => 'New window'
  );
    
  public function configure()
  {
    parent::configure();
    
    $this->widgetSchema['legend'] = new sfWidgetFormInputText();
    $this->validatorSchema['legend'] = new sfValidatorString(array(
      'required' => false
    ));
    
    $this->widgetSchema['legend']->setLabel('Alt');
    
    $methods = $this->getI18n()->translateArray($this->getResizeMethods());
    
    $this->widgetSchema['method'] = new sfWidgetFormSelect(array(
      'choices' => $methods
    ));
    $this->validatorSchema['method'] = new sfValidatorChoice(array(
      'choices' => array_keys($methods),
      'required' => false
    ));

    $this->widgetSchema['background'] = new sfWidgetFormInputText(array(), array('size' => 7));
    $this->validatorSchema['background'] = new sfValidatorString(array(
      'required' => false
    ));

    $this->widgetSchema['quality'] = new sfWidgetFormInputText(array(), array('size' => 5));
    $this->validatorSchema['quality'] = new sfValidatorInteger(array(
      'required' => false,
      'min' => 0,
      'max' => 100
    ));
    $this->widgetSchema['quality']->setLabel('JPG quality');
    $this->widgetSchema->setHelp('quality', 'Leave empty to use default quality');

    $this->widgetSchema['link'] = new sfWidgetFormInputText();
    $this->validatorSchema['link'] = new dmValidatorLinkUrl(array('required' => false));
    $this->widgetSchema->setHelp('link', 'Drag & Drop a page or enter an url');

    $this->widgetSchema['target'] = new sfWidgetFormSelect(array(
        'choices' => $this->getI18n()->translateArray($this->target)        
    ));    
    $this->validatorSchema['target'] = new sfValidatorChoice(array(
        'choices' => array_keys($this->target),
        'required' => false
    ));
    if (is_null($this->getDefault('target'))) $this->setDefault ('target', '_self');
    
    $this->mergePostValidator(
      new sfValidatorCallback(array('callback' => array($this, 'checkBackground')))
    );
  }
  
  public function getFirstDefaults()
  {
    return array_merge(parent::getFirstDefaults(), array(
      'method'      => dmConfig::get('image_resize_method', 'center'),
      'background'  => 'FFFFFF'
    ));
  }

  public function getResizeMethods()
  {
    return array(
      'fit'     => 'Fit',
      'center'  => 'Center',
      'scale'   => 'Scale', 
      'inflate' => 'Inflate',
      'left'    => 'Left',
      'right'   => 'Right',
      'top'     => 'Top',
      'bottom'  => 'Bottom',
    );
  }
  
  public function checkBackground($validator, $values)
  {
    if ('fit' == $values['method'] && !dmString::hexColor($values['background']))
    {
      throw new sfValidatorErrorSchema($validator, array('background' => new sfValidatorError($validator, 'This is not a valid hexadecimal color')));
    }

    return $values;
  }

  protected function renderContent($attributes)
  {
    return $this->getHelper()->renderPartial('dmWidget', 'forms/dmWidgetContentImage', array(
      'form' => $this,
      'hasMedia' => (boolean) $this->getValueOrDefault('mediaId')
    ));
  }

  public function getWidgetValues()
  {
    $values = parent::getWidgetValues();

    if($media = dmDb::table('DmMedia')->findOneByIdWithFolder($values['mediaId']))
    {
      if ($media->isImage())
      {
        if (empty($values['width']))
        {
          if ($values['widget_width'])
          {
            $values['width'] = $values['widget_width'];
            $values['height'] = (int) ($media->getHeight() * ($values['widget_width'] / $media->getWidth()));
          }
          else
          {
            $values['width'] = $media->getWidth();
          }
        }
        elseif (empty($values['height']))
        {
          $values['height'] = (int) ($media->getHeight() * ($values['width'] / $media->getWidth()));
        }
      }
    }

    unset($values['widget_width']);

    $values['background'] = dmArray::get($values, 'background', $this->getFirstDefault('background'), true);

    $values['method'] = dmArray::get($values, 'method', $this->getFirstDefault('method'), true);

    return $values;
  }
}