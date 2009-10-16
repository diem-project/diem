<?php

class dmUpdateSeoService extends dmService
{
  protected static
  $truncateCache;
  
  protected
  $markdown,
  $titlePrefix,
  $titleSuffix;

  public function execute(array $onlyModules = array())
  {
    if(empty($onlyModules))
    {
      $onlyModules = dmContext::getInstance()->getModuleManager()->getProjectModules();
    }
    
    $onlyModules = dmModuleManager::removeModulesChildren($onlyModules);
    
    $culture = dm::getUser()->getCulture();
    
    $this->markdown = dmContext::getInstance()->get('markdown');
    
    $this->titlePrefix = dmConfig::get('title_prefix');
    $this->titleSuffix = dmConfig::get('title_suffix');

    $this->log("dmUpdateSeo::execute");

    $timer = dmDebug::timerOrNull('dmUpdateSeo::execute');

    foreach($onlyModules as $module)
    {
      $this->updateRecursive($module, $culture);
    }

    $timer && $timer->addTime();
  }

  public function updateRecursive($module, $culture)
  {
    if (!$module->hasPage())
    {
      foreach($module->getChildren() as $child)
      {
        $this->updateRecursive($child, $culture);
      }
      return;
    }

//    dm::getUser()->logAlert('update seo on module '.$module);
    
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
    ->leftJoin('p.Translation t on t.id = p.id AND t.lang = ?', $culture)
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
    ->withI18n($culture, $module->getModel())
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
      $parentSlugs = $this->getParentSlugs($module, $culture);
    }

    $modifiedPages = array();
    foreach($pages as $page)
    {
//      if (isset($record['is_active']))
//      {
//        $page->is_active = $record['is_active'];
//      }

      $record = $records[$page['record_id']];
      $parentId = $pageTable->createQuery('p')
      ->select('p.id as id')
      ->where("p.lft < ? AND p.rgt > ?", array($page['lft'], $page['rgt']))
      ->orderBy("p.rgt asc")
      ->limit(1)
      ->fetchValue();
      $parentSlug = isset($parentSlugs[$parentId]) ? $parentSlugs[$parentId] : '';

      $modifiedFields = $this->updatePage($page, $record, $patterns, $parentSlug, $culture);
      
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
            $modifiedFields['lang'] = $culture;
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
      $this->updateRecursive($child, $culture);
    }
  }

  public function updatePage(array $page, myDoctrineRecord $record, $patterns, $parentSlug, $culture)
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
            
            $processMarkdown = 
            $usedRecord->getTable()->hasColumn($field) &&
            false !== strpos(dmArray::get($usedRecord->getTable()->getColumnDefinition($field), 'extra'), 'markdown');
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
        $value = $this->titlePrefix.strtr($pattern, $replacements).$this->titleSuffix;
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

  protected function getParentSlugs($module, $culture)
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
    ->where('p.module = ? AND p.action = ? AND p.id = t.id AND t.lang = ?', array($parentPageModuleKey, $parentPageActionKey, $culture))
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