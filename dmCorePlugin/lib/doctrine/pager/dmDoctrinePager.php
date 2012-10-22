<?php

class dmDoctrinePager extends sfDoctrinePager
{
	protected
	$resultsCache,
	$isInited;
	

	/**
	 * @see sfPager
	 */
	public function setMaxPerPage($maxPerPage)
	{
		parent::setMaxPerPage($maxPerPage);

		return $this;
	}

	/**
	 * @see sfPager
	 */
	public function setQuery($query)
	{
		parent::setQuery($query);

		return $this;
	}

	/**
	 * @see sfPager
	 */
	public function setPage($page)
	{
		parent::setPage($page);

		return $this;
	}

	/**
	 * @see sfPager
	 */
	public function init($force = false)
	{
		if(!$this->isInited || $force){
			parent::init();
			$this->resultsCache = null;
		}
		$this->isInited = true;

		return $this;
	}

	/**
	 * Get all the results for the pager instance, and cache them
	 *
	 * @param mixed $hydrationMode A hydration mode identifier
	 *
	 * @return Doctrine_Collection|array
	 */
	public function getResults($hydrationMode = null)
	{
		if (null === $this->resultsCache)
		{
			$this->resultsCache = parent::getResults($hydrationMode)->getData();
		}

		return $this->resultsCache;
	}

	/**
	 * Get all the results for the pager instance
	 *
	 * @param mixed $hydrationMode A hydration mode identifier
	 *
	 * @return Doctrine_Collection|array
	 */
	public function getResultsWithoutCache($hydrationMode = null)
	{
		return parent::getResults($hydrationMode)->getData();
	}

	public function serialize()
	{
		$vars = get_object_vars($this);
		unset($vars['query'], $vars['resultsCache']);
		return serialize($vars);
	}

	public function getCountQuery()
	{
		$selectQuery = $this->getQuery();

		if (count($selectQuery->getDqlPart('where')) || count($selectQuery->getDqlPart('from')) > 1)
		{
			$query = clone $selectQuery;
			return $query->offset(0)->limit(0);
		}
		else
		{
			return dmDb::table($this->getClass())->createQuery();
		}
	}

	/**
	 * Get the query for the pager.
	 *
	 * @return Doctrine_Query
	 */
	public function getQuery($withI18n = false)
	{
		if($withI18n && dmDb::table($this->class)->hasI18n())
		{
			return parent::getQuery()->withI18n();
		}
		return parent::getQuery();
	}

	/**
	 *
	 * @return boolean
	 */
	public function hasQuery()
	{
		return isset($this->query);
	}
}