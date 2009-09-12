<?php

class dmSearchEngineActions extends dmAdminBaseActions
{

	public function executeIndex(dmWebRequest $request)
	{
		$this->index = $this->getDmContext()->getSearchEngine();

		$this->form = $this->getSearchForm();

		if ($this->query = trim($request->getParameter('query')))
		{
			$this->form->bind($request->getParameterHolder()->getAll());
			$this->pager = $this->getSearchPager($this->query);
		}
		else
		{
			$this->pager = null;
		}
	}

	public function executeReload()
	{
		$this->getDmContext()->getFilesystem()->sf('dm:search-update');

		return $this->redirect('dmSearchEngine/index');
	}

	protected function getSearchPager($query)
	{
    $timeStart = microtime(true);

    $results = $this->index->search($query);
    
		$this->time = sprintf("%01.2f", (microtime(true) - $timeStart));
		
		if (!empty($results))
		{
			$pager = new dmSearchPager($results, 20);
			$pager->setPage($this->getRequestParameter('page', 1));
			$pager->init();
//			$pager->setUrlFormat($form->getUrlFormat());
		}
		else
		{
			$pager = null;
		}
		
		return $pager;
	}

	protected function getSearchForm()
	{
		$form = new dmForm();
		$form->setName('search');
		$form->setWidgets(array('query' => new sfWidgetFormInputText()));
		$form->setValidators(array('query' => new sfValidatorString()));

		return $form;
	}

}