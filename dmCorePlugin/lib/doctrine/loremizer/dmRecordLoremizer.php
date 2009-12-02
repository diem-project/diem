<?php

/*
 * Will TRY to randomly fill empty record fields.
 * It will fail in many case.
 */
class dmRecordLoremizer
{
  protected $record;

  public static function loremize($classOrObject, $override = false, $createAssociations = false)
  {
    if ($classOrObject instanceof dmDoctrineRecord)
    {
      $object = $classOrObject;
    }
    else
    {
      if (!Doctrine_Core::isValidModelClass($classOrObject))
      {
        throw new dmException(sprintf('%s is not a valid model', $classOrObject));
      }
      $object = dmDb::create($classOrObject);
    }

    $loremizer = new self($object);

    return $loremizer->execute($override, $createAssociations);
  }

  public function __construct(myDoctrineRecord $record)
  {
    $this->record = $record;
  }

  public function execute($override = false, $createAssociations = false)
  {
    $this->record->clearRelated();
    
    foreach($this->record->getTable()->getHumanColumns() as $columnName => $column)
    {
      /*
       * Non override on existing records
       * pass if columns value is different than its default value
       */
      if (!$override && (!$this->record->isNew() || $this->record->get($columnName) !== $this->record->getTable()->getDefaultValueOf($columnName)))
      {
        continue;
      }
      if (!dmArray::get($column, 'notnull') && !dmArray::get($column, 'unique') && !rand(0, 2))
      {
        $this->record->set($columnName, null);
        continue;
      }
      if ($columnName == 'slug' && $this->record->getTable()->hasTemplate('Sluggable'))
      {
        continue;
      }

      if ($localRelation = $this->record->getTable()->getRelationHolder()->getLocalByColumnName($columnName))
      {
        $val = $this->getRandomId($localRelation->getTable());
      }
      else
      {
        $val = $this->getRandomValue($columnName, $column);
      }

      $this->record->set($columnName, $val);
    }
    
    if ($createAssociations)
    {
      foreach($this->record->getTable()->getRelationHolder()->getAssociations() as $relation)
      {
        if (rand(0, 3))
        {
          $ids = dmDb::query($relation->getClass().' t')
          ->select('t.id')
          ->orderBy('RANDOM()')
          ->limit(rand(1, 5))
          ->fetchPDO();
          
          foreach($ids as $index => $array)
          {
            $ids[$index] = $array[0];
          }
          
          $this->record->link($relation->getAlias(), array_unique($ids));
        }
      }
    }
    
    return $this->record;
  }

  protected function getRandomValue($columnName, $column)
  {
    switch($column['type'])
    {
      case 'string':
        $val = $this->getStringValForColumn($column);
        break;
      case 'boolean':
        $val = (bool)rand(0,1);
        break;
      case 'blob':
      case 'clob':
        if (strpos(dmArray::get($column, 'extra'), 'markdown') !== false)
        {
          $val = dmLorem::getMarkdownLorem(1/*rand(1, 3)*/);
        }
        else
        {
          $val = dmLorem::getBigLorem();
        }
        break;
      case 'time':
      case 'timestamp':
        $val = rand(0, time());
        break;
      case 'date':
        $val = date("Y-m-d", rand(0, time()));
        break;
      case 'enum':
        $val = dmArray::get($column['values'], array_rand($column['values']));
        break;
      case 'integer':
        $val = rand(0,100000);
        break;
      case 'float':
      case 'decimal':
        $val = rand(0,10000000)/1000;
        break;
      default:
        throw new dmException(sprintf('Diem can not generate random content for %s column', $columnName));
    }

    return $val;
  }

  protected function getStringValForColumn($column)
  {
    if (dmArray::get($column, 'email'))
    {
      return dmString::random(rand(4, 30)).'@localhost.com';
    }
    
    $val = trim(dmLorem::getLittleLorem(rand(min(4, $column['length']), min(40, $column['length']))));

    if (dmArray::get($column, 'unique'))
    {
      $rlen = rand(4, 10);
      $val = substr($val.dmString::random($rlen), 0, $column['length']);
    }

    if (dmArray::get($column, 'nospace'))
    {
      $val = str_replace(' ', '-', $val);
    }

    return $val;
  }

  protected function getRandomId(myDoctrineTable $table)
  {
    try
    {
      $id = dmDb::query($table->getComponentName().' t')
      ->select('t.id')
      ->orderBy('RANDOM()')
      ->limit(1)
      ->fetchValue();
    }
    catch(Exception $e)
    {
      throw new dmException(sprintf('Error while getting %s random id for %s : %s', $table->getComponentName(), $this->record, $e));
    }

    return $id ? $id : null;
  }
}