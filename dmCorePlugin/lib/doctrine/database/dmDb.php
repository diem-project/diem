<?php

class dmDb
{
  protected static
  $tables;

  /**
   * Shortcut for myDoctrineQuery::create
   *
   * @return myDoctrineQuery the query with filled from
   */
  public static function query($from = null, $conn = null)
  {
    if ($from instanceof Doctrine_Connection)
    {
      $conn = $from;
      $from = null;
    }

    $query = new myDoctrineQuery($conn);

    if ($from)
    {
      $query->from($from);
    }

    return $query;
  }

  /**
   * Shortcut for Doctrine_Core::getTable
   * @return myDoctrineTable the table for this model class name
   */
  public static function table($class)
  {
    return Doctrine_Core::getTable(dmString::camelize($class));
  }

  /**
   * Create, populate and return a new record
   * @return myDoctrineRecord the new, hydrated, non-saved record
   */
  public static function create($class, array $values = array())
  {
    return self::table($class)->create($values);
  }

  /**
   * @return PDOStatement
   */
  public static function pdo($query, array $values = array(), Doctrine_Connection $conn = null)
  {
    $conn = null === $conn ? Doctrine_Manager::getInstance()->getCurrentConnection() : $conn;
    
    $stmt = $conn->prepare($query)->getStatement();
    $stmt->execute($values);
    
    return $stmt;
  }
}