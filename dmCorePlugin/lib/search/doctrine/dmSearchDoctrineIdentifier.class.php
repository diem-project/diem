<?php
/**
 * Identifies a Doctrine model.
 *
 * @package sfPropelSearch
 * @subpackage Service
 * @author Carl Vondrick
 */
final class dmSearchDoctrineIdentifier implements xfIdentifier
{
  /**
   * The model we are identifying.
   *
   * @var string
   */
  private $name;

  /**
   * The query
   *
   * @var myDoctrineQuery
   */
  private $query;

  /**
   * The validator methods.  All of these methods must return true on the object
   * for it to match.
   */
  private $validatorMethods = array();
  
  private $discoverStep = 250;
  
  private $hydrationMode = Doctrine::HYDRATE_RECORD;

  /**
   * Constructor to set initial name.
   *
   * @param string $name The model name.
   */
  public function __construct($name)
  {
    $this->name = $name;
  }
  
  /**
   * Adds a validator method.
   *
   * @param string $method The method
   */
  public function addValidator($method)
  {
    $this->validatorMethods[] = $method;
  }
  
  /**
   * Sets the discovery criteria.
   *
   * @param Criteria $c The criteria
   */
  public function setQuery(myDoctrineQuery $query)
  {
    $q = clone $query;

    $q->limit($this->discoverStep);

    $this->query = $q;
  }

  /**
   * @see xfIdentifier
   */
  public function getName()
  {
    return 'dmDoctrine-'.$this->name;
  }

  /**
   * @see xfIdentifier
   */
  public function getGuid($input)
  {
    return 'd' . md5($this->getName() . '-' . $input->getOid());
  }

  /**
   * @see xfIdentifier
   */
  public function match($input)
  {
    if (get_class($input) === $this->name) // don't use instanceof 
    {
      foreach ($this->validatorMethods as $method)
      {
        if (!$input->$method())
        {
          return self::MATCH_IGNORED;
        }
      }
      return self::MATCH_YES;
    }

    return self::MATCH_NO;
  }

  /**
   * @see xfIdentifier
   */
  public function discover($count)
  {
    $q = clone $this->query;
    $q->offset($count * $this->discoverStep);
    
    return $q->execute(array(), $this->hydrationMode);
  }
}
