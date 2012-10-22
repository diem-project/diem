<?php

class dmTableLoremizer extends dmConfigurable
{
  protected
  $serviceContainer;

  public function __construct(sfServiceContainer $serviceContainer, array $options = array())
  {
    $this->serviceContainer = $serviceContainer;
    
    $this->initialize($options);
  }
  
  public function initialize(array $options)
  {
    $this->configure($options);
    
    $this->checkExistingDmMedia();
  }
  
  public function getDefaultOptions()
  {
    return array(
      'nb_records' => 10,
      'create_associations' => true
    );
  }

  public function execute(dmDoctrineTable $table, $nbRecords = null)
  {
    if(null !== $nbRecords)
    {
      $this->setOption('nb_records', $nbRecords);
    }
    
    $collection = new myDoctrineCollection($table);

    if(method_exists($table, 'getRecordLoremizerClass'))
    {
      $recordLoremizerClass = $table->getRecordLoremizerClass();
    }
    else
    {
      $recordLoremizerClass = null;
    }
    
    $loremizer = $this->serviceContainer->getService('record_loremizer', $recordLoremizerClass)
    ->setOption('create_associations', false);
    
    for($it = $table->count(); $it < $this->getOption('nb_records'); $it++)
    {
      $collection->add($loremizer->execute($table->create()));
    }
    
    if($collection->count())
    {
      $collection->save();
    }
    
    if($this->getOption('create_associations'))
    {
      $this->executeAssociations($table);
    }
  }
  
  public function executeAssociations(dmDoctrineTable $table)
  {
    foreach($table->getRelationHolder()->getAssociations() as $association)
    {
      $refTable     = $association->getAssociationTable();

      $nbAssociations = $this->getOption('nb_records') * min(2, $association->getTable()->count());
      
      try
      {
        $availableIds = $this->getAvailableIdsByLocalAlias($refTable);
      }
      catch(dmRecordException $e)
      {
        // no available records
        break;
      }
      
      $createdPairs = $this->getExistingRelRecordPairs($refTable);

      $collection = new myDoctrineCollection($refTable);

      $iterations = 0;

      while(count($createdPairs) < $nbAssociations)
      {
        // avoid infinite loop
        if(++$iterations > 1000)
        {
          break;
        }
        
        $vals = array();
        
        foreach($refTable->getRelationHolder()->getLocals() as $relationAlias => $relation)
        {
          $vals[$relation->getLocalColumnName()] = array_rand($availableIds[$relationAlias]);
        }

        if(!in_array($vals, $createdPairs))
        {
        	//check if association does not already exists
        	
          $collection->add($refTable->create($vals));
          
          $createdPairs[] = $vals;
        }
      }
      
      if($collection->count())
      {
        $collection->save();
      }
    }
  }

  protected function getExistingRelRecordPairs(dmDoctrineTable $refTable)
  {
    $columns = array();
    foreach($refTable->getRelationHolder()->getLocals() as $relationAlias => $relation)
    {
      $columns[] = $relation->getLocalColumnName();
    }

    if(2 !== count($columns))
    {
    	$columns = array_unique($columns);
    	//fix: whenever association is badly made, same column can appear twice
    	if(2 !== count($columns))
    	{
      	return array();
    	}
    }
    
    return dmDb::pdo(sprintf(
      'SELECT r.%s, r.%s FROM %s r',
      $columns[0],
      $columns[1],
      $refTable->getTableName()
    ))->fetchAll(PDO::FETCH_ASSOC);
  }
  
  protected function getAvailableIdsByLocalAlias(dmDoctrineTable $refTable)
  {
    $availableIds = array();
    
    foreach($refTable->getRelationHolder()->getLocals() as $relationAlias => $relation)
    {
      $tmp = dmDb::pdo('SELECT t.id FROM '.$relation->getTable()->getTableName().' t')->fetchAll(PDO::FETCH_NUM);
      
      if(empty($tmp))
      {
        throw new dmRecordException();
      }
      
      foreach($tmp as $t)
      {
        $availableIds[$relationAlias][$t[0]] = $t[0];
      }
    }
    
    return $availableIds;
  }

  protected function checkExistingDmMedia()
  {
    if (!dmDb::table('DmMedia')->count())
    {
      $defaultFilePath = dmOs::join(sfConfig::get('dm_core_dir'), 'data/image', 'defaultMedia.jpg');

      require_once(dmOs::join(sfConfig::get('sf_symfony_lib_dir'), 'validator/sfValidatorFile.class.php'));

      dmDb::table('DmMedia')->create(array(
        'dm_media_folder_id' => dmDb::table('DmMediaFolder')->getTree()->fetchRoot()->get('id')
      ))
      ->create(new sfValidatedFile(
        'defaultMedia.jpg',
        'image/jpeg',
        $defaultFilePath,
        filesize($defaultFilePath)
      ))
      ->save();
    }
  }
}