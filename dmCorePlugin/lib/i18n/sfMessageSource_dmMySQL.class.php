<?php

class sfMessageSource_dmMySQL extends sfMessageSource_MySQL
{

  /**
   * Constructor.
   * Creates a new message source using MySQL.
   *
   * Diem addition :
   * Will guess dsn source from Doctrine configuration
   *
   * @param string $source  MySQL datasource, in PEAR's DB DSN format.
   * @see MessageSource::factory();
   */
  function __construct($source)
  {
  	if ($source == 'default')
  	{
  		$conn = Doctrine_Manager::connection();
  		
  		$source = sprintf('mysql://%s:%s@%s',
  		  $conn->getOption('username'),
  		  $conn->getOption('password'),
  		  preg_replace('|^mysql\:host=([^;]+);dbname=(.+)$|', '$1/$2', $conn->getOption('dsn'))
  		);
  	}
    
  	parent::__construct($source);
  }
  
   /**
   * Gets an array of messages for a particular catalogue and cultural variant.
   *
   * @param string $variant the catalogue name + variant
   * @return array translation messages.
   */
  public function &loadData($variant)
  {
    $variant = mysql_real_escape_string($variant, $this->db);

    $statement =
      "SELECT t.source, t.target
        FROM trans_unit t, catalogue c
        WHERE c.name = '{$variant}'
          AND c.cat_id = t.cat_id";

    $rs = mysql_query($statement, $this->db);

    $result = array();

    while ($row = mysql_fetch_array($rs, MYSQL_NUM))
    {
    	$result[utf8_encode($row[0])] = array(
        utf8_encode($row[1]), //target
        '', //id
        '' //comments
    	);
    }

    return $result;
  }
}