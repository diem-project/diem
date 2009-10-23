<?php

class dmSeoSynchronizer
{
  protected static
  $truncateCache;
  
  protected
  $moduleManager,
  $markdown,
  $culture,
  $titlePrefix,
  $titleSuffix;
  
  public function __construct(dmModuleManager $moduleManager, dmMarkdown $markdown, $culture)
  {
    $this->moduleManager  = $moduleManager;
    $this->markdown       = $markdown;
    $this->culture        = $culture;
  }
  
  public function setCulture($culture)
  {
    $this->culture = $culture;
  }

  public function execute(array $onlyModules = array())
  {
    if(empty($onlyModules))
    {
      $onlyModules = $this->moduleManager->getProjectModules();
    }
    
    $onlyModules = dmModuleManager::removeModulesChildren($onlyModules);
    
    $this->titlePrefix = (string) dmConfig::get('title_prefix');
    $this->titleSuffix = (string) dmConfig::get('title_suffix');

    $timer = dmDebug::timerOrNull('dmUpdateSeo::execute');

    foreach($onlyModules as $module)
    {
      $this->updateRecursive($module);
    }

    $timer && $timer->addTime();
  }

  public function updateRecursive($module)
  {
    if (!$module->hasPage())
    {
      foreach($module->getChildren() as $child)
      {
        $this->updateRecursive($child);
      }
      
      return;
    }

    $pageTable = dmDb::table('DmPage');

    /*
     * get autoSeo patterns
     */
    $patternArray = dmDb::query('DmAutoSeo as')
    ->select('as.slug, as.name, as.title, as.h1, as.description, as.keywords')
    ->where('as.module = ? AND as.action = ?', array($module->getKey(), 'show'))
    ->limit(1)
    ->fetchArray();

    if(empty($patternArray))
    {
      $patterns = dmDb::table('DmAutoSeo')->createFromModuleAndAction($module, 'show')->saveGet()->toArray();
    }
    else
    {
      $patterns = $patternArray[0];
    }

    $pageAutoSeoFields = DmPage::getAutoSeoFields();
    foreach($patterns as $field => $pattern)
    {
      if (!in_array($field, $pageAutoSeoFields))
      {
        unset($patterns[$field]);
      }
    }
    
    if (isset($patterns['keywords']) && !sfConfig::get('dm_seo_use_keywords'))
    {
      unset($patterns['keywords']);
    }

    /*
     * get pages
     */
    $timerGetPages = dmDebug::timerOrNull('update seo get pages');
    $pdoPages = $pageTable->createQuery('p')
    ->leftJoin('p.Translation t on t.id = p.id AND t.lang = ?', $this->culture)
    ->where('p.module = ? AND p.action = ?', array($module->getKey(), 'show'))
    ->select('p.id, p.lft, p.rgt, p.record_id, t.auto_mod, t.slug, t.name, t.title, t.h1, t.description, t.keywords, t.id')
    ->fetchPDO();

    $pages = array();
    foreach($pdoPages as $p)
    {
      $pages[$p[0]] = array(
        'id' => $p[0],
        'lft' => $p[1],
        'rgt' => $p[2],
        'record_id' => $p[3],
        'auto_mod' => $p[4],
        'slug' => $p[5],
        'name' => $p[6],
        'title' => $p[7],
        'h1' => $p[8],
        'description' => $p[9],
        'keywords' => $p[10],
        'exists' => (bool) $p[11]
      );
    }

    unset($pdoPages);

    $timerGetPages && $timerGetPages->addTime();

    /*
     * get records
     */
    $records = $module->getTable()->createQuery('r INDEXBY r.id')
    ->withI18n($this->culture, $module->getModel())
    ->fetchRecords();

    /*
     * get parent slugs
     * if slug pattern starts with a /
     * we don't use parent slug to build  the page slug
     */
    if ($patterns['slug']{0} === '/')
    {
      $parentSlugs = array();
    }
    else
    {
      $parentSlugs = $this->getParentSlugs($module);
    }

    $modifiedPages = array();
    foreach($pages as $page)
    {
      $record = $records[$page['record_id']];
      $parentId = $pageTable->createQuery('p')
      ->select('p.id as id')
      ->where("p.lft < ? AND p.rgt > ?", array($page['lft'], $page['rgt']))
      ->orderBy("p.rgt asc")
      ->limit(1)
      ->fetchValue();
      $parentSlug = isset($parentSlugs[$parentId]) ? $parentSlugs[$parentId] : '';

      $modifiedFields = $this->updatePage($page, $record, $patterns, $parentSlug);
      
      if (!empty($modifiedFields))
      {
        $modifiedPages[$page['id']] = $modifiedFields;
      }
    }
    
    $records->free();

    /*
     * Save modifications
     */
    if(!empty($modifiedPages))
    {
      $conn = Doctrine_Manager::connection();
      try
      {
        $conn->beginTransaction();

        foreach($modifiedPages as $id => $modifiedFields)
        {
          if (!$pages[$id]['exists'])
          {
            $modifiedFields['id'] = $id;
            $modifiedFields['lang'] = $this->culture;
            $translation = new DmPageTranslation();
            $translation->fromArray($modifiedFields);
            $conn->unitOfWork->processSingleInsert($translation);
          }
          else
          {
            #TODO try to extract query creation from foreach
            Doctrine_Query::create()->update('DmPageTranslation')
            ->set($modifiedFields)
            ->where('id = ?', $id)
            ->execute();
          }
        }

        $conn->commit();
      }
      catch(Doctrine_Exception $e)
      {
        $conn->rollback();
        throw $e;
      }
    
    }
    
    unset($pages);

    foreach($module->getChildren() as $child)
    {
      $this->updateRecursive($child);
    }
  }

  public function updatePage(array $page, dmDoctrineRecord $record, $patterns, $parentSlug)
  {
    $pageAutoMod = dmArray::get($page, 'auto_mod', 'snthdk');

    foreach($patterns as $field => $pattern)
    {
      if (strpos($pageAutoMod, $field{0}) === false)
      {
        unset($patterns[$field]);
      }
    }

    /*
     * Calculate replacements
     */

    $module = $record->getDmModule();
    $moduleModel = $module->getModel();
    $moduleKey = $module->getKey();

    preg_match_all('/%([\w\d\.-]+)%/i', implode('', $patterns), $results);

    $placeholders = array_unique($results[1]);

    $replacements = array();

    foreach ($placeholders as $placeholder)
    {
      try
      {
        /*
         * Extract model and field from "model.field" or "model"
         */
        if (strpos($placeholder, "."))
        {
          list($usedModuleKey, $field) = explode(".", $placeholder);
        }
        else
        {
          $usedModuleKey = $placeholder;
          $field = '__toString';
        }

        $usedModuleKey = dmString::modulize($usedModuleKey);
        $usedRecord = null;
        /*
         * Retrieve used record
         */
        if ($usedModuleKey == $moduleKey)
        {
          $usedRecord = $record;
        }
        else
        {
          $usedRecord = $record->getAncestorRecord($usedModuleKey);
        }

        if ($usedRecord instanceof dmDoctrineRecord)
        {
          /*
           * get record value for field
           */
          if ($field == '__toString')
          {
            $usedValue = $usedRecord->__toString();
            $processMarkdown = true;
          }
          else
          {
            $usedValue = $usedRecord->get($field);
            
            $processMarkdown = $usedRecord->getTable()->hasColumn($field) && $usedRecord->getTable()->isMarkdownColumn($field);
          }
          
          unset($usedRecord);
        }
        else
        {
          $usedValue = $moduleKey.'-'.$usedModuleKey.' not found';
          $processMarkdown = false;
        }
        
        $usedValue = trim($usedValue);
        
        if($processMarkdown)
        {
          $usedValue = $this->markdown->toText($usedValue);
        }

        $replacements[$this->wrap($placeholder)] = $usedValue;
      }
      catch(Exception $e)
      {
        throw $e;
        $replacements[$this->wrap($placeholder)] = "[ ".$placeholder." : ".$e->getMessage()." ]";
      }
    }

    /*
     * Assign replacements to patterns
     */
    $values = array();
    foreach($patterns as $field => $pattern)
    {
      if ($field === 'slug')
      {
        $slugReplacements = array();
        foreach($replacements as $key => $replacement)
        {
          $slugReplacements[$key] = dmString::slugify($replacement);
        }
        
        // add parent slug
        $value = $parentSlug.'/'.strtr($pattern, $slugReplacements);

        $value = trim(preg_replace('|(/{2,})|', '/', $value), '/');
      }
      elseif($field === 'title')
      {
        $value = $this->titlePrefix.ucfirst(strtr($pattern, $replacements)).$this->titleSuffix;
      }
      else
      {
        $value = strtr($pattern, $replacements);
      }

      $values[$field] = $this->truncateValueForField(trim($value), $field);
    }

    /*
     * Compare obtained seo values to page values
     */
    $modifiedFields = array();
    foreach($values as $field => $value)
    {
      if ($value != $page[$field])
      {
        $modifiedFields[$field] = $value;
      }
    }

    return $modifiedFields;
  }

  public function wrap($property)
  {
    return '%'.$property.'%';
  }

  protected function getParentSlugs($module)
  {
    if($module->hasListPage())
    {
      $parentPageModuleKey = $module->getKey();
      $parentPageActionKey = 'list';
    }
    elseif ($parentModule = $module->getNearestAncestorWithPage())
    {
      $parentPageModuleKey = $parentModule->getKey();
      $parentPageActionKey = 'show';
    }
    else
    {
      throw new dmException(sprintf(
        'can not identify parent module for %s module', $module
      ));
    }

    $parentSlugResults = dmDb::table('DmPage')->createQuery('p')
    ->leftJoin('DmPageTranslation t')
    ->select('t.id, t.slug')
    ->where('p.module = ? AND p.action = ? AND p.id = t.id AND t.lang = ?', array($parentPageModuleKey, $parentPageActionKey, $this->culture))
    ->fetchPDO();

    $parentSlugs = array();
    foreach($parentSlugResults as $psr)
    {
      $parentSlugs[$psr[0]] = $psr[1];
    }
    unset($parentSlugsResult);

    return $parentSlugs;
  }

  /*
   * Static methods
   */

  public static function truncateValueForField($value, $field)
  {
    return mb_substr($value, 0, self::getFieldMaxLength($field));
  }

  public static function getFieldMaxLength($field)
  {
    if (null === self::$truncateCache)
    {
      $truncateConfig = sfConfig::get('dm_seo_truncate');
      self::$truncateCache = array();
      foreach(DmPage::getAutoSeoFields() as $seoField)
      {
        self::$truncateCache[$seoField] = dmArray::get($truncateConfig, $seoField, 255);
      }
    }

    return self::$truncateCache[$field];
  }
}