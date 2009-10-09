  protected function addSearchQuery($query)
  {
    if (!$search = trim($this->getSearch()))
    {
      return $query;
    }

    return $this->processSearchQuery($query, $search);
  }

  protected function getSearch()
  {
    return $this->getUser()->getAppliedSearchOnModule('<?php echo $this->getModuleName() ?>');
  }

  protected function setSearch($search)
  {
    $this->getUser()->setAttribute('<?php echo $this->getModuleName() ?>.search', $search, 'admin_module');
  }
