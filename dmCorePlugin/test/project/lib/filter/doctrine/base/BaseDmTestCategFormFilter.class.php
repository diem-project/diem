<?php

/**
 * DmTestCateg filter form base class.
 *
 * @package    retest
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class BaseDmTestCategFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {


		if($this->needsWidget('id')){
			$this->setWidget('id', new sfWidgetFormDmFilterInput());
			$this->setValidator('id', new sfValidatorDoctrineChoice(array('required' => false, 'model' => 'DmTestCateg', 'column' => 'id')));
		}
		if($this->needsWidget('created_at')){
			$this->setWidget('created_at', new sfWidgetFormChoice(array('choices' => array(
        ''      => '',
        'today' => $this->getI18n()->__('Today'),
        'week'  => $this->getI18n()->__('Past %number% days', array('%number%' => 7)),
        'month' => $this->getI18n()->__('This month'),
        'year'  => $this->getI18n()->__('This year')
      ))));
			$this->setValidator('created_at', new sfValidatorChoice(array('required' => false, 'choices' => array_keys($this->widgetSchema['created_at']->getOption('choices')))));
		}
		if($this->needsWidget('updated_at')){
			$this->setWidget('updated_at', new sfWidgetFormChoice(array('choices' => array(
        ''      => '',
        'today' => $this->getI18n()->__('Today'),
        'week'  => $this->getI18n()->__('Past %number% days', array('%number%' => 7)),
        'month' => $this->getI18n()->__('This month'),
        'year'  => $this->getI18n()->__('This year')
      ))));
			$this->setValidator('updated_at', new sfValidatorChoice(array('required' => false, 'choices' => array_keys($this->widgetSchema['updated_at']->getOption('choices')))));
		}
		if($this->needsWidget('position')){
			$this->setWidget('position', new sfWidgetFormDmFilterInput());
			$this->setValidator('position', new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))));
		}

		if($this->needsWidget('domains_list')){
			$this->setWidget('domains_list', new sfWidgetFormDmDoctrineChoice(array('multiple' => true, 'model' => 'DmTestDomain', 'expanded' => true)));
			$this->setValidator('domains_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmTestDomain', 'required' => false)));
		}

		if($this->needsWidget('dm_test_domain_categ_list')){
			$this->setWidget('dm_test_domain_categ_list', new sfWidgetFormDmDoctrineChoice(array('multiple' => true, 'model' => 'DmTestDomainCateg', 'expanded' => true)));
			$this->setValidator('dm_test_domain_categ_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmTestDomainCateg', 'required' => false)));
		}
		if($this->needsWidget('posts_list')){
			$this->setWidget('posts_list', new sfWidgetFormDmDoctrineChoice(array('multiple' => true, 'model' => 'DmTestPost', 'expanded' => true)));
			$this->setValidator('posts_list', new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DmTestPost', 'required' => false)));
		}


    
    $this->mergeI18nForm();


    $this->widgetSchema->setNameFormat('dm_test_categ_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function addDomainsListColumnQuery(Doctrine_Query $query, $field, $values)
  {
    if (!is_array($values))
    {
      $values = array($values);
    }

    if (!count($values))
    {
      return;
    }

    $query->leftJoin('r.DmTestDomainCateg DmTestDomainCateg')
          ->andWhereIn('DmTestDomainCateg.domain_id', $values);
  }

  public function getModelName()
  {
    return 'DmTestCateg';
  }

  public function getFields()
  {
    return array(
      'id'           => 'Number',
      'created_at'   => 'Date',
      'updated_at'   => 'Date',
      'position'     => 'Number',
      'id'           => 'Number',
      'name'         => 'Text',
      'is_active'    => 'Boolean',
      'lang'         => 'Text',
      'domains_list' => 'ManyKey',
    );
  }
}
