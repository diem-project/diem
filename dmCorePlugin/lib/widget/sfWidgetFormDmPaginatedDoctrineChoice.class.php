<?php
class sfWidgetFormDmPaginatedDoctrineChoice extends sfWidgetFormDoctrineChoice
{
	protected $pager;

	public function __construct($options = array(), $attributes = array())
	{
		$this->pager = new dmDoctrinePager($options['model']);
		if(isset($options['query']))
		{
			$this->pager->setQuery($options['query']);
		}
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
			if(isset($this->options['query']) && !$this->pager->hasQuery())
			{
				$this->pager->setQuery($this->options['query']);

			}
			$this->pager->init();
			$choices = $this->pager->getResults();
			$this->choices = array();
				
			$method = $this->getOption('method');
			$keyMethod = $this->getOption('key_method');

			foreach($choices as $choice)
			{
				$this->choices[$choice->$keyMethod()] = $choice->$method();
			}
		}
		return $this->choices;
	}

	public function setChoices($choices)
	{
		$this->choices = $choices;
	}

	public function setPager($pager)
	{
		$this->pager = $pager;
	}

	public function getPager()
	{
		//$this->pager->init();
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