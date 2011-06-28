<?php

class DmZoneFrontForm extends DmZoneForm
{

  public function configure()
  {
    parent::configure();
    
    $this->setName($this->name.'_'.$this->object->id);

    $this->useFields(array('id', 'css_class', 'width'));
    
    $this->widgetSchema['css_class']
      ->setAttribute('class', 'dm_zone_css_class')
      ->setLabel('CSS class');
    $this->validatorSchema['css_class'] = new dmValidatorCssClasses(array('required' => false));
    
    $this->widgetSchema['width']
      ->setAttribute('class', 'dm_zone_width');
    $this->validatorSchema['width'] = new dmValidatorCssSize(array('required' => false));
  }

  public function render($attributes = array())
  {
    $attributes = dmString::toArray($attributes, true);

    return
    $this->open($attributes).
    '<ul class="dm_form_elements">'.
    $this->getFormFieldSchema()->render($attributes).
    '</ul>'.
    sprintf(
      '<div class="actions">
        <div class="actions_part clearfix">
          %s%s
        </div>
        <div class="actions_part clearfix">
          %s%s
        </div>
      </div>',
      sprintf('<a class="cancel dm close_dialog dm button fleft">%s</a>', $this->__('Cancel')),
      sprintf('<input type="submit" class="submit try blue fright" name="try" value="%s" />', $this->__('Try')),
      sprintf('<a class="delete dm button red fleft" title="%s">%s</a>', $this->__('Delete this zone'), $this->__('Delete')),
      sprintf('<input type="submit" class="submit and_save green fright" name="and_save" value="%s" />', $this->__('Save and close'))
    ).
    $this->close();
  }


}