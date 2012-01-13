<?php

/**
 * PluginDmTransUnit form.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage filter
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfDoctrineFormFilterPluginTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
abstract class PluginDmTransUnitFormFilter extends BaseDmTransUnitFormFilter
{
  public function setup()
  {
    if($this->needsWidget('dm_catalogue_id'))
    {
      $this->setWidget('dm_catalogue_id', new sfWidgetFormDoctrineChoice(array('model' => 'DmCatalogue', 'add_empty' => true)));
      $this->setValidator('dm_catalogue_id', new sfValidatorDoctrineChoice(array('multiple' => false, 'model' => 'DmCatalogue', 'required' => false)));
    }
    
    parent::setup();
  }
  
  public function getFields()
  {
    $fields = parent::getFields();
    
    $fields['dm_catalogue_id'] = 'Number';

    return $fields;
  }
}
