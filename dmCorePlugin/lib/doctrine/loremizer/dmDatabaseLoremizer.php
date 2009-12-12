<?php

class dmDatabaseLoremizer
{
  protected
  $dispatcher,
  $nbRecordsByTable;

  public function __construct(sfEventDispatcher $dispatcher)
  {
    $this->dispatcher = $dispatcher;
  }

  public function loremize($nbRecordsByTable = 10)
  {
    $this->nbRecordsByTable = $nbRecordsByTable;

    $this->loremizeDmMedia();

    foreach(sfContext::getInstance()->getModuleManager()->getProjectModules() as $module)
    {
      /*
       * Start with root modules
       */
      if ($module->hasParent())
      {
        continue;
      }

      /*
       * Media have already been loremized
       */
      if ($module->getKey() === 'dmMedia')
      {
        continue;
      }

      if (!$module->hasModel())
      {
        continue;
      }

      $this->loremizeModule($module);
    }

    $this->loremizeAssociations();
  }

  protected function loremizeAssociations()
  {
    $nbAssociations = round($this->nbRecordsByTable * 1.5);

    $refClasses = array();
    foreach(sfContext::getInstance()->getModuleManager()->getProjectModules() as $module)
    {
      if ($module->hasModel())
      {
        foreach($module->getTable()->getRelationHolder()->getAssociations() as $association)
        {
          $refClasses[] = $association->getAssociationTable()->getComponentName();
        }
      }
    }

    $refClasses = array_unique($refClasses);

    foreach($refClasses as $refClass)
    {
      $nbExisting = dmDb::table($refClass)->count();
      if ($nbExisting >= $nbAssociations)
      {
        continue;
      }
      $refTable = dmDb::table($refClass);
      $localRelations = $refTable->getRelationHolder()->getLocals();
      for($it = $nbExisting; $it < $nbAssociations; $it++)
      {
        $refObject = new $refClass;
        foreach($localRelations as $relation)
        {
          $id = dmDb::query($relation->getClass().' t')
          ->select('t.id')
          ->orderBy('RANDOM()')
          ->limit(1)
          ->fetchValue();

          $refObject->set($relation->getLocalColumnName(), $id);
        }

        try
        {
          $refObject->save();
        }
        catch(Doctrine_Connection_Exception $e)
        {
          // refObject already exists...
        }
        catch(Doctrine_Validator_Exception $e)
        {
          dmDebug::kill($e->getMessage(), $refObject);
        }
      }
    }
  }

  /*
   * Will generate random associations
   */
  public function loremizeAssociation(myDoctrineTable $table)
  {
    $associationRelations = $table->getRelationHolder()->getAssociations();

    if (!count($associationRelations))
    {
      return;
    }

    $collection = $table->findAll();
    foreach($collection as $record)
    {
      foreach($associationRelations as $alias => $relation)
      {
        $ids = dmDb::query($relation->getClass().' t')
        ->select('t.id')
        ->distinct()
        ->orderBy('RANDOM()')
        ->limit(rand(0, $this->nbAssociationsByRecord))
        ->fetchValues();

        array_walk($ids, create_function('&$a', '$a = $a["t_id"];'));

        foreach($ids as $id)
        {
          $record->link($alias, $ids);
        }
      }
    }

    try
    {
      $collection->save();
    }
    catch(Exception $e)
    {
      throw new dmException('Error while loremizing '.$table->getComponentName.' associations : '.$e->getMessage());
    }
  }

  public function loremizeModule(dmModule $module)
  {
    $table = $module->getTable();

    $nbRecords = $table->count();

    $collection = new myDoctrineCollection($table);

    for($it = $nbRecords; $it < $this->nbRecordsByTable; $it++)
    {
      $collection[] = dmRecordLoremizer::loremize($table->getComponentName());
    }
    
    try
    {
      $collection->save();
    }
    catch(Exception $e)
    {
      throw new dmException('Error while loremizing '.$module.' : '.$e->getMessage());
    }

    foreach($module->getChildren() as $child)
    {
      $this->loremizeModule($child);
    }

    return true;
  }

  public function loremizeDmMedia()
  {
    dmDb::table('DmMediaFolder')->checkRoot()->sync();
    
    $table = dmDb::table('DmMedia');
    
    if (!$table->count())
    {
      $filePath = dmOs::join(sfConfig::get('dm_core_dir'), 'data/image', 'defaultMedia.jpg');

      require_once(dmOs::join(sfConfig::get('sf_symfony_lib_dir'), 'validator/sfValidatorFile.class.php'));

      $file = new sfValidatedFile(
        'defaultMedia.jpg',
        'image/jpeg',
        $filePath,
        filesize($filePath)
      );

      $media = new DmMedia();
      $media->dm_media_folder_id = dmDb::table('DmMediaFolder')->getTree()->fetchRoot()->id;
      $media->create($file);

      $media->save();
    }
  }
}