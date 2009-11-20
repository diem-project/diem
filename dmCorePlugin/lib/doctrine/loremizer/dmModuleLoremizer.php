<?php

class dmModuleLoremizer
{
  protected
  $dispatcher;

  public function __construct(sfEventDispatcher $dispatcher)
  {
    $this->dispatcher = $dispatcher;
  }

  public function loremize(dmModule $module,  $nbMax)
  {
    $this->loremizeDmMedia();
    
    $table = $module->getTable();

    $nbRecords = $table->count();

    $collection = new myDoctrineCollection($table);

    for($it = $nbRecords; $it < $nbMax; $it++)
    {
      $collection[] = dmRecordLoremizer::loremize($table->getComponentName(), false, true);
    }

    try
    {
      $collection->save();
    }
    catch(Exception $e)
    {
//      dmDebug::kill($collection);
      throw new dmException('Error while loremizing '.$module.' : '.$e->getMessage());
    }

    return true;
  }

  protected function loremizeDmMedia()
  {
    $databaseLoremizer = new dmDatabaseLoremizer($this->dispatcher);
    
    $databaseLoremizer->loremizeDmMedia();
  }
  
}