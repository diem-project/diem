  protected function addSearchQuery($query)
  {
    if (!$search = trim($this->getSearch()))
    {
      return $query;
    }
    
    $searchParts = explode(' ', $search);
    
    $alias = $query->getRootAlias();
    
    foreach($searchParts as $searchPart)
    {
      $ors = array();
      $params = array();
      
      foreach($this->getDmModule()->getTable()->getColumns() as $columnName => $column)
      {
        switch($column['type'])
        {
          case 'blob':
          case 'clob':
          case 'string':
          case 'enum':
            $ors[] = $alias.'.'.$columnName.' LIKE ?';
            $params[] = '%'.$searchPart.'%';
            break;
          case 'integer':
          case 'float':
          case 'decimal':
            if (is_numeric($searchPart))
            {
              $ors[] = $alias.'.'.$columnName.' = ?';
              $params[] = $searchPart;
            }
            break;
          case 'boolean':
          case 'time':
          case 'timestamp':
          case 'date':
          default:
        }
      }
      
      if(count($ors))
      {
        $query->addWhere(implode(' OR ', $ors), $params);
      }
    }

    return $query;
  }

  protected function getSearch()
  {
    return $this->getUser()->getAppliedSearchOnModule('<?php echo $this->getModuleName() ?>');
  }

  protected function setSearch($search)
  {
    $this->getUser()->setAttribute('<?php echo $this->getModuleName() ?>.search', $search, 'admin_module');
  }
