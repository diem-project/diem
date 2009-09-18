<?php

class dmSearchIndexGroup extends dmSearchIndexCommon
{
  /**
   * The indices this group holds
   *
   * @var array
   */
  protected
  $indices = array();
  
  public function getIndices()
  {
    return $this->indices;
  }

  /**
   * Adds an index
   *
   * @param string $name The index name
   * @param xfIndex $index The index to add
   */
  public function addIndex($name, dmSearchIndexCommon $index)
  {
    $this->indices[$name] = $index;
  }

  /**
   * Gets an index
   *
   * @param string $name The index name
   */
  public function getIndex($name)
  {
    $this->setup();

    if (!isset($this->indices[$name]))
    {
      throw new dmException('Index "' . $name . '" could not be found.');
    }

    return $this->indices[$name];
  }
  
  protected function configure()
  {
    foreach(sfConfig::get('dm_i18n_cultures') as $culture)
    {
      $index = new dmSearchIndex($culture);
      $this->addIndex($index->getName(), $index);
    }
  }
  
  public function insert(DmPage $page)
  {
    $this->setup();

    foreach ($this->getIndices() as $index)
    {
      $index->insert($page);
    }
  }

  public function remove(DmPage $page)
  {
    $this->setup();

    foreach ($this->getIndices() as $index)
    {
      $index->remove($page);
    }
  }
  
  public function refresh(DmPage $page)
  {
    $this->remove($page);
    $this->insert($page);
  }

  public function populate()
  {
    $this->setup();

    $start = microtime(true);

    $this->getLogger()->log('Populating group...', $this->getName());
    
    foreach ($this->getIndices() as $name => $index)
    {
      $this->getLogger()->log('Populating index "' . $name . '"...', $this->getName());

      $index->populate();
    }

    $this->getLogger()->log('Group populated in "' . round(microtime(true) - $start, 2) . '" seconds.', $this->getName());
  }
  
  public function optimize()
  {
    $this->setup();

    $start = microtime(true);

    $this->getLogger()->log('Optimizing group...', $this->getName());
    
    foreach ($this->getIndices() as $name => $index)
    {
      $index->optimize();
    }

    $this->getLogger()->log('Group optimized in "' . round(microtime(true) - $start, 2) . '" seconds.', $this->getName());
  }
  
  /**
   * @see xfIndex
   */
  public function describe()
  {
    $this->setup();

    $response = array();

    foreach ($this->getIndices() as $name => $index)
    {
      $response[$name] = $index->describe();
    }

    return $response;
  }
  
}