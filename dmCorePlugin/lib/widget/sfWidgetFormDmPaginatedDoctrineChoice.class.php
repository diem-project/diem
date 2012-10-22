<?php
class sfWidgetFormDmPaginatedDoctrineChoice extends sfWidgetFormDoctrineChoice
{
	protected $pager;
	protected $inited = false;

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


	public function init($force = false)
	{
		if(!$this->inited || $force)
		{
			if(!$this->pager->hasQuery() && $query = $this->getOption('query', false))
			{
				$this->pager->setQuery($query);
			}

      $this->pager->init();
			$this->inited = true;
		}

		return $this;
	}

	public function getChoices()
	{
		if(!isset($this->cache_choices))
		{
			$this->init();
			$choices = $this->getOption('choices', false);
			if(!$choices) 
			{
				$choices = $this->pager->getResults();
			}
			$this->cache_choices = array();

			$method = $this->getOption('method');
			$keyMethod = $this->getOption('key_method');

			if($choices && count($choices) > 0 && is_object($choices[key($choices)]))
			{
				foreach($choices as $choice)
				{
					$this->cache_choices[$choice->$keyMethod()] = $choice->$method();
				}
			}
			elseif(isset($this->choices))
			{
				$this->cache_choices = $this->choices;
			}
		}
		return $this->cache_choices;
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