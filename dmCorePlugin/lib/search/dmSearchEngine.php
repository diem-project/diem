<?php

class dmSearchEngine extends dmSearchIndexGroup
{

  protected function initialize(array $options)
  {
    parent::initialize($options);
    
    $this->setName(get_class($this));
    
    $this->createIndices();
  }
  
  public function setDir($dir)
  {
    $this->setOption('dir', $dir);
    
    foreach($this->getIndices() as $index)
    {
      $index->setOption('dir', $dir.'/'.$index->getName());
    }
  }
  
  protected function createIndices()
  {
    $this->indices = array();
    
    /*
     * Create one index per culture
     */
    foreach($this->serviceContainer->getService('i18n')->getCultures() as $culture)
    {
      $name = 'dm_page_'.$culture;
      
      $this->serviceContainer->mergeParameter('search_index.options', array(
        'culture' => $culture,
        'name'    => $name,
        'dir'     => $this->getOption('dir').'/'.$name
      ));
      
      $this->addIndex($name, $this->serviceContainer->getService('search_index'));
    }
  }
  
  public function search($query)
  {
    return $this->getCurrentIndex()->search($query);
  }
  
  public function getCurrentIndex()
  {
    return $this->getIndex('dm_page_'.$this->serviceContainer->getParameter('user.culture'));
  }
}