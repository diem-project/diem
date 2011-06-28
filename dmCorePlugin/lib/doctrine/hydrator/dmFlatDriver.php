<?php
/**
 * Get results directly and skip hydration. Uses PDO::FETCH_COLUMN
 *
 * @package     Doctrine
 * @subpackage  Hydrate
 * @since       1.2
 */
class Doctrine_Hydrator_dmFlatDriver extends Doctrine_Hydrator_Abstract
{
  public function hydrateResultSet($stmt)
  {
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
  }
}