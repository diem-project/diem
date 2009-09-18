<?php

class DmZoneFrontForm extends DmZoneForm
{

  public function configure()
  {
    parent::configure();
    
    $this->setName($this->name.'_'.$this->object->id);

    unset($this['dm_area_id'], $this['position'], $this['updated_at']);
    
    $this->widgetSchema['css_class']->setAttribute('class', 'dm_zone_css_class');
    
    $this->widgetSchema['width']->setAttribute('class', 'dm_zone_width');

    $this->validatorSchema['width'] = new dmValidatorCssSize(array(
      'required' => false
    ));
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
      sprintf('<a class="cancel dm close_dialog dm button fleft">%s</a>', dm::getI18n()->__('Cancel')),
      sprintf('<input type="submit" class="submit try blue fright" name="try" value="%s" />', dm::getI18n()->__('Try')),
      sprintf('<a class="delete dm button red fleft" title="%s">%s</a>', dm::getI18n()->__('Delete this zone'), dm::getI18n()->__('Delete')),
      sprintf('<input type="submit" class="submit and_save green fright" name="and_save" value="%s" />', dm::getI18n()->__('Save and close'))
    ).
    $this->close();
  }


}