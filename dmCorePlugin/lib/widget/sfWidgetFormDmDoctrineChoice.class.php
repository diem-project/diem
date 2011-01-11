<?php
class sfWidgetFormDmDoctrineChoice extends sfWidgetFormDoctrineChoice
{
  protected $pager;
  
  public function __construct($options = array(), $attributes = array())
  {
    $this->pager = new dmDoctrinePager($options['model']);
    $options['translate_choices'] = false;
    parent::__construct($options, $attributes);
  }
  
  public function configure($options = array(), $attributes = array())
  {
    $this->addOption('maxPerPage', 10);
    parent::configure($options, $attributes);
    $this->pager->setMaxPerPage($this->getOption('maxPerPage'));
  }
  
  public function getChoices()
  {
    if(!isset($this->choices))
    {
      $choices = $this->pager->getResults();
      $this->choices = array();
      foreach($choices as $choice)
      {
        $this->choices[$choice->getPrimaryKey()] = $choice;
      }
    }
    return $this->choices;
  }
  
  public function getPager()
  {
    $this->pager->init();
    return $this->pager;
  }
  
  public function getRenderer()
  {
    if ($this->getOption('renderer'))
    {
      return $this->getOption('renderer');
    }

    if (!$class = $this->getOption('renderer_class'))
    {
      $type = !$this->getOption('expanded') ? '' : ($this->getOption('multiple') ? 'checkbox' : 'radio');
      $class = sprintf('sfWidgetFormSelect%s', ucfirst($type));
    }

    return new $class(array_merge(array('choices' => new sfCallable(array($this, 'getChoices'))), $this->options['renderer_options']), $this->getAttributes());
  }
}