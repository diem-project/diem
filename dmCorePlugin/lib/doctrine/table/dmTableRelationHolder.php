<?php

class dmTableRelationHolder
{
  protected
    $table,
    $relations,
    $associationRelations,
    $foreignRelations,
    $localRelations;

  public function __construct(myDoctrineTable $table)
  {
    $this->table = $table;
  }

  public function getAll()
  {
    if (null !== $this->relations)
    {
      return $this->relations;
    }

    return $this->relations = $this->table->getRelations();
  }

  public function get($alias)
  {
    return dmArray::get($this->getAll(), $alias);
  }

  public function getByClass($class)
  {
    foreach($this->getAll() as $relation)
    {
      if ($relation->getClass() === $class)
      {
        return $relation;
      }
    }

    return null;
  }

  public function has($alias)
  {
    return array_key_exists($alias, $this->getAll());
  }

  public function getForeigns()
  {
    if (null !== $this->foreignRelations)
    {
      return $this->foreignRelations;
    }

    $this->foreignRelations = array();

    foreach($this->getAll() as $alias => $relation)
    {
      if ($alias === 'Translation')
      {
        continue;
      }

      if ($relation instanceof Doctrine_Relation_ForeignKey)
      {
        $this->foreignRelations[$alias] = $relation;
      }
    }

    return $this->foreignRelations;
  }

  public function getLocals()
  {
    if (null !== $this->localRelations)
    {
      return $this->localRelations;
    }

    $this->localRelations = array();

    foreach($this->getAll() as $alias => $relation)
    {
      if ($relation instanceof Doctrine_Relation_LocalKey)
      {
        $this->localRelations[$alias] = $relation;
      }
    }

    return $this->localRelations;
  }

  public function getLocalByColumnName($columnName)
  {
    foreach($this->getLocals() as $local)
    {
      if ($local->getLocalColumnName() === $columnName)
      {
        return $local;
      }
    }

    return null;
  }

  public function getLocalByClass($class)
  {
    foreach($this->getLocals() as $local)
    {
      if ($local->getClass() === $class)
      {
        return $local;
      }
    }

    return null;
  }


  /*
   * @return array LocalKey Relations with class === DmMedia
   */
  public function getLocalMedias()
  {
    $relations = array();

    foreach($this->getLocals() as $alias => $relation)
    {
      if ($relation['class'] === 'DmMedia')
      {
        $relations[$alias] = $relation;
      }
    }

    return $relations;
  }

  public function getAssociations()
  {
    if (null !== $this->associationRelations)
    {
      return $this->associationRelations;
    }

    $this->associationRelations = array();

    foreach($this->getAll() as $alias => $relation)
    {
      if ($relation instanceof Doctrine_Relation_Association)
      {
        $this->associationRelations[$alias] = $relation;
      }
    }

    return $this->associationRelations;
  }

  public function getAssociationByClass($class)
  {
    foreach($this->getAssociations() as $association)
    {
      if ($association->getClass() === $class)
      {
        return $association;
      }
    }

    return null;
  }

}