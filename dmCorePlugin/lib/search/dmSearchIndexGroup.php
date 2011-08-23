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
    if (!isset($this->indices[$name]))
    {
      throw new dmException('Index "' . $name . '" could not be found.');
    }

    return $this->indices[$name];
  }
  
  public function populate()
  {
    $start = microtime(true);

    foreach ($this->getIndices() as $name => $index)
    {
      $this->serviceContainer->getService('logger')->log($this->getName().': Populating index "' . $name . '"...');

      $index->populate();
    }

    $this->serviceContainer->getService('logger')->log($this->getName().': Group populated in "' . round(microtime(true) - $start, 2) . '" seconds.');
  
    $this->serviceContainer->getService('logger')->log('-----> Search index population successfully completed');
    
    $this->fixPermissions();
    
    return $this;
  }
  
  public function optimize()
  {
    $start = microtime(true);

    $this->serviceContainer->getService('logger')->log($this->getName().' : Optimizing group...');
    
    $this->fixPermissions();
    
    foreach($this->getIndices() as $index)
    {
      $index->optimize();
    }

    $this->serviceContainer->getService('logger')->log($this->getName().' : Group optimized in "' . round(microtime(true) - $start, 2) . '" seconds.');
    
    $this->fixPermissions();
    
    return $this;
  }
  
  /**
   * @see xfIndex
   */
  public function describe()
  {
    $response = array();

    foreach ($this->getIndices() as $name => $index)
    {
      $response[$name] = $index->describe();
    }

    return $response;
  }
}