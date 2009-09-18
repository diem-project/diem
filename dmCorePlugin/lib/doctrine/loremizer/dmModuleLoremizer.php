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
    dmDb::table('DmMediaFolder')->checkRoot()->sync();
    
    $table = $module->getTable();

    $nbRecords = $table->count();

    $collection = new myDoctrineCollection($table);

    for($it = $nbRecords; $it < $nbMax; $it++)
    {
      $collection[] = dmRecordLoremizer::loremize($table->getComponentName());
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

  
}