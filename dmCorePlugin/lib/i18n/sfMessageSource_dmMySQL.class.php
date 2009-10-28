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

  /**
   * Loads a particular message catalogue. Use read() to
   * to get the array of messages. The catalogue loading sequence
   * is as follows:
   *
   *  # [1] Call getCatalogueList($catalogue) to get a list of variants for for the specified $catalogue.
   *  # [2] For each of the variants, call getSource($variant) to get the resource, could be a file or catalogue ID.
   *  # [3] Verify that this resource is valid by calling isValidSource($source)
   *  # [4] Try to get the messages from the cache
   *  # [5] If a cache miss, call load($source) to load the message array
   *  # [6] Store the messages to cache.
   *  # [7] Continue with the foreach loop, e.g. goto [2].
   *
   * @param  string  $catalogue a catalogue to load
   * @return boolean always true
   * @see    read()
   */
  public function load($catalogue = 'messages')
  {
    $variants = $this->getCatalogueList($catalogue);

    $this->messages = array();

    foreach ($variants as $variant)
    {
      $source = $this->getSource($variant);

      if ($this->isValidSource($source) == false)
      {
        continue;
      }

      $loadData = true;

      if ($this->cache)
      {
        $data = $this->cache->get($variant.':'.$this->culture);

        if (is_array($data))
        {
          $this->messages[$variant] = $data;
          $loadData = false;
        }

        unset($data);
      }

      if ($loadData)
      {
        $data = &$this->loadData($source);
        if (is_array($data))
        {
          $this->messages[$variant] = $data;
          if ($this->cache)
          {
            $this->cache->set($variant.':'.$this->culture, $data);
          }
        }

        unset($data);
      }
    }

    return true;
  }
}