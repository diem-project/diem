<?php

class sfMessageSource_dm extends sfMessageSource
{
  protected
  $catalogueTable = 'dm_catalogue',
  $transUnitTable = 'dm_trans_unit';

  public function __construct()
  {
    
  }
  
  /**
   * Gets an array of messages for a particular catalogue and cultural variant.
   *
   * @param string $variant the catalogue name + variant
   * @return array translation messages.
   */
  public function &loadData($variant)
  {
    $raw = dmDb::pdo('SELECT t.source, t.target FROM '.$this->transUnitTable.' t, '.$this->catalogueTable.' c WHERE c.name = ? AND c.id = t.dm_catalogue_id', array($variant))
    ->fetchAll(PDO::FETCH_NUM);
    
    $results = array();
    
    foreach($raw as $data)
    {
      $results[$data[0]] = $data[1];
    }
    
    unset($raw);
    
    return $results;
  }  
  
  /**
   * Checks if a particular catalogue+variant exists in the database.
   *
   * @param string $variant catalogue+variant
   * @return boolean true if the catalogue+variant is in the database, false otherwise.
   */ 
  public function isValidSource($variant)
  {
    $result = dmDb::pdo('SELECT EXISTS(SELECT 1 FROM '.$this->catalogueTable.' WHERE name = ?)', array($variant))
    ->fetch(PDO::FETCH_NUM);
    
    return $result[0];
  }

  /**
   * Gets all the variants of a particular catalogue.
   *
   * @param string $catalogue catalogue name
   * @return array list of all variants for this catalogue.
   */
  public function getCatalogueList($catalogue)
  {
    $variants = explode('_', $this->culture);

    $catalogues = array($catalogue);

    $variant = null;

    for ($i = 0, $max = count($variants); $i < $max; $i++)
    {
      if (strlen($variants[$i]) > 0)
      {
        $variant .= $variant ? '_'.$variants[$i] : $variants[$i];
        $catalogues[] = $catalogue.'.'.$variant;
      }
    }

    return array_reverse($catalogues);
  }

  public function getId()
  {
    return md5($this->source);
  }

  public function catalogues()
  {
    throw new dmException('not implemented');
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
  
  /**
   * Saves the list of untranslated blocks to the translation source. 
   * If the translation was not found, you should add those
   * strings to the translation source via the <b>append()</b> method.
   *
   * @param string $catalogue the catalogue to add to
   * @return boolean true if saved successfuly, false otherwise.
   */
  function save($catalogue = 'messages')
  {
    $messages = $this->untranslated;
    $details = $this->getCatalogueList($catalogue);
    // get DmCatalogue object with units indexedby source
    $dmCatalogue = dmDb::query('DmCatalogue c')->leftJoin('c.Units u INDEXBY u.source')->where('name = ?', $details[0])->fetchOne();
    if (!($dmCatalogue instanceof DmCatalogue)) {
      throw new dmException(sprintf("The catalog \"%s\" for culture \"%s\" could not be found.", $catalogue, $this->culture));
    }
    
    /* @var $units Doctrine_Collection */
    // get Units as Doctrine_Collection for easy manipulation
    $units = $dmCatalogue->get('Units');
    foreach ($messages as $message) {
      // get unit (creates new one if not exists
      $dmTransUnit = $units->get($message);
      $dmTransUnit->set('dm_catalogue_id', $dmCatalogue->get('id'));
      // set unit to collection
      $units->set($message, $dmTransUnit);
    }
    // save collection
    $units->save();
  }

  /**
   * Deletes a particular message from the specified catalogue.
   *
   * @param string $message   the source message to delete.
   * @param string $catalogue the catalogue to delete from.
   * @return boolean true if deleted, false otherwise. 
   */
  function delete($message, $catalogue = 'messages')
  {
    $details = $this->getCatalogueList($catalogue);
    // get DmCatalogue object
    $dmCatalogue = dmDb::query('DmCatalogue c')->leftJoin('c.Units u INDEXBY u.source')->where('name = ?', $details[0])->fetchOne();
    if (!($dmCatalogue instanceof DmCatalogue)) {
      throw new dmException(sprintf("The catalog \"%s\" could not be found.\n"));
    }
    // get DmTransUnit object
    $dmTransUnit = dmDb::query('DmTransUnit')->where('source = ?', $message)->andWhere('dm_catalogue_id = ?', $dmCatalogue->get('id'))->fetchOne();
    if ($dmTransUnit instanceof DmTransUnit) {
      $dmTransUnit->delete();
    }
  }

  /**
   * Updates the translation.
   *
   * @param string $text      the source string.
   * @param string $target    the new translation string.
   * @param string $comments  comments
   * @param string $catalogue the catalogue of the translation.
   * @return boolean true if translation was updated, false otherwise. 
   */
  function update($text, $target, $comments, $catalogue = 'messages')
  {
    $details = $this->getCatalogueList($catalogue);
    // get DmCatalogue object
    $dmCatalogue = dmDb::query('DmCatalogue c')->leftJoin('c.Units u INDEXBY u.source')->where('name = ?', $details[0])->fetchOne();
    if (!($dmCatalogue instanceof DmCatalogue)) {
      throw new dmException(sprintf("The catalog \"%s\" could not be found.\n"));
    }
    $dmTransUnit = dmDb::query('DmTransUnit')->where('source = ?', $text)->andWhere('dm_catalogue_id = ?', $dmCatalogue->get('id'))->fetchOne();
    if ($dmTransUnit instanceof DmTransUnit) {
      $dmTransUnit->set('target', $target);
      $dmTransUnit->set('meta', $comments);
      $dmTransUnit->save();
    }
  }
}