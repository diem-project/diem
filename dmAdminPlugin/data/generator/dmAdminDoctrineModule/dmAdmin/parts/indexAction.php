  public function executeIndex(sfWebRequest $request)
  {
    // searching
    if ($request->hasParameter('search'))
    {
      $this->setSearch($request->getParameter('search'));
      $request->setParameter('page', 1);
    }
    
    // sorting
    if ($request->getParameter('sort') && $this->isValidSortColumn($request->getParameter('sort')))
    {
      $this->setSort(array($request->getParameter('sort'), $request->getParameter('sort_type')));
    }
    
    // pager
    if ($request->getParameter('page'))
    {
      $this->setPage($request->getParameter('page'));
    }

    $this->pager = $this->getPager();
    $this->sort = $this->getSort();
    
  }
