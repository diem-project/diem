<?php

class dmSeoSynchronizer
{
  protected static
  $truncateCache,
  $patternsPlaceholdersCache = array(),
  $shouldProcessMarkdownCache = array(),
  $moduleIsActivatable = array();
  
  protected
  $moduleManager,
  $culture,
  $nodeParentIdStmt;
  
  public function __construct(dmModuleManager $moduleManager)
  {
    $this->moduleManager  = $moduleManager;
  }
  
  public function setCulture($culture)
  {
    $this->culture = $culture;
  }
  
  public function execute(array $onlyModules, $culture)
  {
    $this->setCulture($culture);
    
    $recordDefaultCulture = myDoctrineRecord::getDefaultCulture();
    myDoctrineRecord::setDefaultCulture($this->culture);
    
    if(empty($onlyModules))
    {
      $onlyModules = $this->moduleManager->getProjectModules();
    }
    elseif(is_string(dmArray::first($onlyModules)))
    {
      $onlyModules = $this->moduleManager->keysToModules($onlyModules);
    }
    
    $onlyModules = dmModuleManager::removeModulesChildren($onlyModules);
    
    foreach($onlyModules as $module)
    {
      $this->updateRecursive($module);
    }
    
    myDoctrineRecord::setDefaultCulture($recordDefaultCulture);
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

    /*
     * get autoSeo patterns
     */
    $autoSeoRecord = dmDb::query('DmAutoSeo a')
    ->withI18n($this->culture, null, 'a')
    ->where('a.module = ?', $module->getKey())
    ->andWhere('a.action = ?', 'show')
    ->fetchOne();
    
    if(!$autoSeoRecord)
    {
      $autoSeoRecord = dmDb::table('DmAutoSeo')
      ->createFromModuleAndAction($module->getKey(), 'show', $this->culture)
      ->saveGet();
    }

    $autoSeoRecordTranslation = $autoSeoRecord->getOrCreateCurrentTranslation();
    
    $patterns = array();
    foreach(DmPage::getAutoSeoFields() as $patternField)
    {
      $patterns[$patternField] = $autoSeoRecordTranslation->get($patternField);
    }
    
    if (isset($patterns['keywords']) && !sfConfig::get('dm_seo_use_keywords'))
    {
      unset($patterns['keywords']);
    }

    /*
     * get pages
     */
    $pdoPages = dmDb::pdo('
    SELECT p.id, p.lft, p.rgt, p.record_id, t.auto_mod, t.slug, t.name, t.title, t.h1, t.description, t.keywords, t.is_active, t.id as exist
    FROM dm_page p LEFT JOIN dm_page_translation t ON (t.id = p.id AND t.lang = ?)
    WHERE p.module = ? AND p.action = ?', array($this->culture, $module->getKey(), 'show')
    )->fetchAll(PDO::FETCH_ASSOC);

    $pages = array();
    foreach($pdoPages as $p)
    {
      $pages[$p['id']] = $p;
    }
    unset($pdoPages);
    
    /*
     * get records
     */
    $records = $module->getTable()->createQuery('r INDEXBY r.id')
    ->withI18n($this->culture, $module->getModel(), 'r')
    ->fetchRecords()
    ->getData();
    
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

      if(!empty($parentSlugs))
      {
        $parentId = $this->getNodeParentId($page);
        $parentSlug = isset($parentSlugs[$parentId]) ? $parentSlugs[$parentId] : '';
      }
      else
      {
        $parentSlug = '';
      }

      $modifiedFields = $this->updatePage($page, $module, $record, $patterns, $parentSlug);
      
      if (!empty($modifiedFields))
      {
        $modifiedPages[$page['id']] = $modifiedFields;
      }
    }
    
    /*
     * Save modifications
     */
    if(!empty($modifiedPages))
    {
      /*
       * Fix bug when no DmPage instance have been loaded yet
       * ( this can happen when synchronization is run in a thread )
       * DmPageTranslation class does not exist
       */
      if (!class_exists('DmPageTranslation'))
      {
        new DmPage();
      }
      
      $conn = Doctrine_Manager::getInstance()->getCurrentConnection();
      try
      {
        $conn->beginTransaction();

        foreach($modifiedPages as $id => $modifiedFields)
        {
          if (!$pages[$id]['exist'])
          {
            $modifiedFields['id'] = $id;
            $modifiedFields['lang'] = $this->culture;
            $translation = new DmPageTranslation();
            $translation->fromArray($modifiedFields);

            $conn->unitOfWork->processSingleInsert($translation);

            if(array_key_exists('slug', $modifiedFields))
            {
              // verify the slug is not already in use
              if(!dmDb::table('DmPage')->isSlugUnique($translation->get('slug'), $id))
              {
                myDoctrineQuery::create($conn)->update('DmPageTranslation')
                ->set(array('slug' => dmDb::table('DmPage')->createUniqueSlug($translation->get('slug'), $id, $parentSlug)))
                ->where('id = ?', $id)
                ->andWhere('lang = ?', $this->culture)
                ->execute();
              }
            }
          }
          else
          {
            if(array_key_exists('slug', $modifiedFields))
            {
              // verify the slug is not already in use
              if(!dmDb::table('DmPage')->isSlugUnique($modifiedFields['slug'], $id))
              {
                $modifiedFields['slug'] = dmDb::table('DmPage')->createUniqueSlug($modifiedFields['slug'], $id, $parentSlug);
              }
            }
            
            myDoctrineQuery::create($conn)->update('DmPageTranslation')
            ->set($modifiedFields)
            ->where('id = ?', $id)
            ->andWhere('lang = ?', $this->culture)
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

  public function updatePage(array $page, dmProjectModule $module, dmDoctrineRecord $record, $patterns, $parentSlug)
  {
    $pageAutoMod = $page['exist'] ? $page['auto_mod'] : 'snthdk';
    
    if('snthdk' !== $pageAutoMod)
    {
      foreach($patterns as $field => $pattern)
      {
        if (false === strpos($pageAutoMod, $field{0}))
        {
          unset($patterns[$field]);
        }
      }
    }

    /*
     * Calculate replacements
     */
    $replacements = $this->getReplacementsForPatterns($module, $patterns, $record);

    /*
     * Assign replacements to patterns
     */
    $values = $this->compilePatterns($patterns, $replacements, $parentSlug);
    
    /*
     * Compare obtained seo values with page values
     */
    $modifiedFields = array();
    foreach($values as $field => $value)
    {
      if ($value != $page[$field])
      {
        $modifiedFields[$field] = $value;
      }
    }

    $modifiedFields = $this->updatePageIsActive($page, $module, $record, $modifiedFields);

    return $modifiedFields;
  }

  protected function updatePageIsActive(array $page, dmProjectModule $module, dmDoctrineRecord $record, array $modifiedFields)
  {
    if ($this->shouldUpdatePageIsActiveForModule($module))
    {
      if (!$page['exist'] || $page['is_active'] != $record->get('is_active'))
      {
        $modifiedFields['is_active'] = $record->get('is_active');
      }
    }

    return $modifiedFields;
  }

  protected function shouldUpdatePageIsActiveForModule(dmProjectModule $module)
  {
    if(!isset(self::$moduleIsActivatable[$module->getKey()]))
    {
      self::$moduleIsActivatable[$module->getKey()] = $module->getTable()->hasField('is_active');
    }

    return self::$moduleIsActivatable[$module->getKey()];
  }

  public function validatePattern(dmProjectModule $module, $field, $pattern, dmDoctrineRecord $record = null)
  {
    $record = null === $record ? $module->getTable()->findOne() : $record;

    try
    {
      $this->getReplacementsForPatterns($module, array($pattern), $record);
    }
    catch(Exception $e)
    {
      if(sfConfig::get('dm_debug'))
      {
        throw $e;
      }
      
      return false;
    }
    
    return true;
  }
  
  public function getReplacementsForPatterns(dmProjectModule $module, array $patterns, dmDoctrineRecord $record)
  {    
    $moduleKey = $module->getKey();
    $replacements = array();
    
    foreach(self::getPatternsPlaceholders($patterns) as $placeholder)
    {
      if ('culture' === $placeholder || 'user.culture' === $placeholder)
      {
        $replacements[$this->wrap($placeholder)] = $this->culture;
        continue;
      }
      /*
       * Extract model and field from 'model.field' or 'model'
       */
      if (strpos($placeholder, '.'))
      {
        list($usedModuleKey, $field) = explode('.', $placeholder);
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
      if ($usedModuleKey === $moduleKey)
      {
        $usedRecord = $record;
      }
      elseif($module->hasAncestor($usedModuleKey))
      {
        $usedRecord = $record->getAncestorRecord($module->getAncestor($usedModuleKey)->getModel());
      }
      else
      {
        $usedRecord = $record->getRelatedRecord($this->moduleManager->getModule($usedModuleKey)->getModel());
      }

      if ($usedRecord instanceof dmDoctrineRecord)
      {
        /*
         * get record value for field
         */
        if ($field === '__toString')
        {
          $usedValue = $usedRecord->__toString();
          $processMarkdown = true;
        }
        else
        {
          try
          {
            $usedValue = $usedRecord->get($field);
          }
          catch(Doctrine_Record_UnknownPropertyException $e)
          {
            $usedValue = $usedRecord->{'get'.dmString::camelize($field)}();
          }
          
          $processMarkdown = self::shouldProcessMarkdown($usedRecord->getTable(), $field);
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
        $usedValue = dmMarkdown::brutalToText($usedValue);
      }
      
      $replacements[$this->wrap($placeholder)] = $usedValue;
    }
    
    return $replacements;
  }
  
  public function compilePatterns(array $patterns, array $replacements, $parentSlug)
  {
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
        
        $value = strtr($pattern, $slugReplacements);
        
        // add parent slug
        if ($pattern{0} !== '/')
        {
          $value = $parentSlug.'/'.$value;
        }
        
        $value = trim($value, '/');

        if(false !== strpos($value, '//'))
        {
          $value = preg_replace('|(/{2,})|', '/', $value);
        }
      }
      elseif($field === 'title')
      {
        $value = ucfirst(strtr($pattern, $replacements));
      }
      else
      {
        $value = strtr($pattern, $replacements);
      }

      $values[$field] = self::truncateValueForField(trim($value), $field);
    }
    
    return $values;
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

    $parentSlugResults = dmDb::pdo('SELECT t.id, t.slug
    FROM dm_page p, dm_page_translation t
    WHERE p.module = ? AND p.action = ? AND p.id = t.id AND t.lang = ?',
    array($parentPageModuleKey, $parentPageActionKey, $this->culture))
    ->fetchAll(PDO::FETCH_NUM);
    
    $parentSlugs = array();
    foreach($parentSlugResults as $psr)
    {
      $parentSlugs[$psr[0]] = $psr[1];
    }
    unset($parentSlugsResult);

    return $parentSlugs;
  }
  
  protected function getNodeParentId(array $pageData)
  {
    if (null === $this->nodeParentIdStmt)
    {
      $this->nodeParentIdStmt = Doctrine_Manager::getInstance()->getCurrentConnection()->prepare('SELECT p.id
FROM dm_page p
WHERE p.lft < ? AND p.rgt > ?
ORDER BY p.rgt ASC
LIMIT 1')->getStatement();
    }

    $this->nodeParentIdStmt->execute(array($pageData['lft'], $pageData['rgt']));
    
    return $this->nodeParentIdStmt->fetchColumn();
  }

  protected static function getPatternsPlaceholders(array $patterns)
  {
    $flatPatterns = implode('', $patterns);

    if(isset(self::$patternsPlaceholdersCache[$flatPatterns]))
    {
      return self::$patternsPlaceholdersCache[$flatPatterns];
    }

    preg_match_all('/%([\w\d\.-]+)%/i', $flatPatterns, $results);

    return self::$patternsPlaceholdersCache[$flatPatterns] = array_unique($results[1]);
  }

  protected static function shouldProcessMarkdown(dmDoctrineTable $table, $field)
  {
    $key = $table->getComponentName().'.'.$field;

    if(isset(self::$shouldProcessMarkdownCache[$key]))
    {
      return self::$shouldProcessMarkdownCache[$key];
    }

    return self::$shouldProcessMarkdownCache[$key] = $table->hasField($field) && $table->isMarkdownColumn($field);
  }

  /**
   * Static methods
   */

  public static function truncateValueForField($value, $field)
  {
    return function_exists('mb_substr')
    ? mb_substr($value, 0, self::getFieldMaxLength($field))
    : substr($value, 0, self::getFieldMaxLength($field));
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