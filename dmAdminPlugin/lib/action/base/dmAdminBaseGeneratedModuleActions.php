<?php

class dmAdminBaseGeneratedModuleActions extends dmAdminBaseActions
{
	protected function getRouteArrayForAction($action, $object = null)
	{
		$route = array('sf_route' => $this->getDmModule()->getUnderscore(), 'action' => $action);

		if (null !== $object)
		{
			$route['pk'] = $object->getPrimaryKey();
		}

		if(sfConfig::get('dm_admin_embedded'))
		{
			$route['dm_embed'] = 1;
		}

		return $route;
	}

	/**
	 * @return dmDoctrineRecord
	 */
	public function getObject($relations = array(), $pk = 'pk')
	{
		if(!isset($this->object))
		{
			$_pk = $this->getRequest()->getParameter($pk, false);
			if(!$_pk) {
				$_pk = $this->getRequest()->getGetParameter($pk, false);
				if(!$_pk) {
					$_pk = $this->getRequest()->getPostParameter($pk, false);
				}
			}

			if(!$_pk)
			{
				$id = $this->getDmModule()->getTable()->getIdentifier();
				$_pk = $this->getRequest()->getParameter($id, false);
			}

			if($_pk){
				$this->object = $this->buildObjectQuery($_pk, empty($relations) ? $this->getRelationsAlias() : $relations)->fetchOne();
			}else{
				$this->object = false;
			}
		}
		return $this->object;
	}

	/**
	 * @param dmWebRequest $request
	 */
	protected function getObjectOrForward404(dmWebRequest $request, $relations = array())
	{
		$this->forward404Unless(
		$this->getObject($relations),
		sprintf('Unable to find the %s object with the following parameters "%s").', $this->getDmModule()->getModel(), str_replace("\n", '', var_export($request->getParameterHolder()->getAll(), true)))
		);

		return $this->object;
	}

	/**
	 * @return dmModule
	 */
	public function getDmModule()
	{
		if (null !== $this->dmModule)
		{
			return $this->dmModule;
		}

		return $this->dmModule = $this->context->getModuleManager()->getModule($this->getModuleKey());
	}

	public function executeLoremize(dmWebRequest $request)
	{
		try
		{
			$this->getService('table_loremizer')->execute($this->getDmModule()->getTable(), $request->getParameter('nb', 10));

			$this->getUser()->logInfo('Successfully loremized');
		}
		catch(Exception $e)
		{
			$this->getUser()->logError('An error occured during loremization');

			if (sfConfig::get('sf_debug'))
			{
				$this->getUser()->logAlert($e->getMessage());
			}

			if (sfConfig::get('dm_debug'))
			{
				throw $e;
			}
		}

		return $this->redirectBack();
	}

	/*
	 * When sorting by a localKey column ( ex: categ_id ),
	 * try to sort with foreign's table identifier column ( ex: categ.name )
	 * Also try to sort with the translation table if any
	 */
	protected function tryToSortWithForeignColumn(Doctrine_Query $query, array $sort)
	{
		$table = $this->getDmModule()->getTable();

		if('integer' === dmArray::get($table->getColumnDefinition($sort[0]), 'type'))
		{
			if($table->isI18nColumn($sort[0]))
			{
				$query->addOrderBy(sprintf('%s.%s %s', $query->getJoinAliasForRelationAlias($table->getComponentName(), 'Translation'), $sort[0], $sort[1]));
				// Success, skip default sorting by local column
				return;
			}
			// If the sort column is a local key, try to sort with foreign table
			elseif ($relation = $table->getRelationHolder()->getLocalByColumnName($sort[0]))
			{
				if ($relation instanceof Doctrine_Relation_LocalKey && ($foreignTable = $relation->getTable()) instanceof dmDoctrineTable)
				{
					if (($foreignColumn = $foreignTable->getIdentifierColumnName()) != 'id')
					{
						if (!$joinAlias = $query->getJoinAliasForRelationAlias($table->getComponentName(), $relation->getAlias()))
						{
							$query->leftJoin(sprintf('%s.%s %s', $query->getRootAlias(), $relation->getAlias(), $relation->getAlias()));
							$joinAlias = $relation->getAlias();

							if($foreignTable->isI18nColumn($foreignColumn))
							{
								$query->leftJoin(sprintf('%s.%s %s', $joinAlias, 'Translation', $joinAlias.'Translation'));
							}
						}

						if($foreignTable->isI18nColumn($foreignColumn))
						{
							$query->addOrderBy(sprintf('%s.%s %s', $joinAlias.'Translation', $foreignColumn, $sort[1]));
						}
						else
						{
							$query->addOrderBy(sprintf('%s.%s %s', $joinAlias, $foreignColumn, $sort[1]));
						}
						// Success, skip default sorting by local column
						return;
					}
				}
			}
		}
		elseif($table->isI18nColumn($sort[0]))
		{
			$query->addOrderBy(sprintf('%s.%s %s', $query->getJoinAliasForRelationAlias($table->getComponentName(), 'Translation'), $sort[0], $sort[1]));
			// Success, skip default sorting by local column
			return;
		}

		if($table->hasField($sort[0]))
		{
			$query->addOrderBy($sort[0] . ' ' . $sort[1]);
		}
	}

	protected function processSortForm($form)
	{
		$request = $this->getRequest();

		if ($request->isMethod('post'))
		{
			if($form->bindAndValid($request))
			{
				try
				{
					$form->save();
				}
				catch(Exception $e)
				{
					if (sfConfig::get('sf_debug'))
					{
						throw $e;
					}

					$this->getUser()->logError($this->getI18n()->__('A problem occured when sorting the items'), true);
				}

				$this->getUser()->logInfo($this->getI18n()->__('The items have been sorted successfully'), true);

				return $this->redirect($this->getRequest()->getUri());
			}
		}
	}

	protected function batchToggleBoolean(array $ids, $field, $value)
	{
		$table = $this->getDmModule()->getTable();
		$value = $value ? 1 : 0;

		if (!$pk = $table->getPrimaryKey())
		{
			throw new dmException(sprintf('Table %s must have exactly one primary key to suppport batch actions', $table->getComponentName()));
		}

		if (!$table->hasField($field))
		{
			throw new dmException(sprintf('Table %s has no field named %s', $table->getComponentName(), $field));
		}

		$query = $table->createQuery('r')->whereIn($pk, $ids);

		if($table->isI18nColumn($field))
		{
			$query->withI18n()->andWhere('rTranslation.'.$field.' = ?', 1-$value);
		}
		else
		{
			$query->andWhere($field.' = ?', 1-$value);
		}

		foreach($query->fetchRecords() as $record)
		{
			$record->set($field, $value);
			$record->save();
		}

		$this->getUser()->logInfo('The selected items have been modified successfully');
	}

	/*
	 * Force download an export of a table
	 * required options : format, extension, encoding, exportClass, module
	 */
	protected function doExport(array $options)
	{
		/*
		 * get data in an array
		 */
		$exportClass = $options['exportClass'];
		$export = new $exportClass($options['module']->getTable(), $this->getI18n());
		$data = $export->generate($options['format']);

		/*
		 * transform into downloadable data
		 */
		switch($options['extension'])
		{
			default:
				$csv = new dmCsvWriter(',', '"');
				$csv->setCharset($options['encoding']);
				$data = $csv->convert($data);
				$mime = 'text/csv';
		}

		$this->download($data, array(
      'file_name' => sprintf('%s-%s_%s.%s',
		dmConfig::get('site_name'),
		$this->getI18n()->__($options['module']->getName()),
		date('Y-m-d'),
		$options['extension']
		),
      'mime_type' => sprintf('%s; charset=%s', $mime, $options['encoding'])
		));
	}

	/*
	 * Search methods
	 */
	protected function processSearchQuery(dmDoctrineQuery $query, $search, $table = null)
	{
		$searchParts = explode(' ', $search);

		$rootAlias = $query->getRootAlias();
		$translationAlias = $rootAlias.'Translation';
		$table = null === $table ? $this->getDmModule()->getTable() : $table;

		$query->withI18n($this->getUser()->getCulture(), $this->getDmModule()->getModel());

		foreach($searchParts as $searchPart)
		{
			$ors = array();
			$params = array();

			foreach($table->getAllColumns() as $columnName => $column)
			{
				$alias = $table->isI18nColumn($columnName) ? $translationAlias : $rootAlias;

				switch($column['type'])
				{
					case 'blob':
					case 'clob':
					case 'string':
					case 'enum':
					case 'date':
						$ors[] = $alias.'.'.$columnName.' LIKE ?';
						$params[] = '%'.$searchPart.'%';
						break;
					case 'integer':
					case 'float':
					case 'decimal':
					case 'double':
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
	}

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
		return $this->getUser()->getAppliedSearchOnModule($this->getSfModule());
	}

	protected function setSearch($search)
	{
		$this->getUser()->setAttribute($this->getSfModule().'.search', $search, 'admin_module');
	}


	/*
	 * History methods
	 */

	public function executeHistory(sfWebRequest $request)
	{
		$this->forward404Unless($this->getDmModule()->getTable()->isVersionable());

		$this->object = $this->getObjectOrForward404($request);

		$this->revisions = $this->object->getVersion();

		// we want an array, not a doctrine collection
		$this->revisions = $this->revisions->getData();

		if (count($this->revisions) > 1)
		{
			usort($this->revisions, create_function('$a, $b',
        'return $a->get(\'version\') < $b->get(\'version\');'
        ));
		}

		$this->dispatcher->notify(new sfEvent($this, 'admin.edit_object', array('object' => $this->object)));

		$this->dispatcher->connect('dm.bread_crumb.filter_links', array($this, 'historyListenToBreadCrumbFilterLinksEvent'));
	}

	public function historyListenToBreadCrumbFilterLinksEvent(sfEvent $event, array $links)
	{
		$links[] = $this->getHelper()->tag('h1', $this->getI18n()->__('Revision history'));

		return $links;
	}

	/*
	 * Sort methods
	 */

	public function executeSortTable(sfWebRequest $request)
	{
		$this->forward404Unless($this->getDmModule()->getTable()->isSortable());

		$this->context->getServiceContainer()->addParameters(array(
      'admin_sort_form.defaults'  => array(),
      'admin_sort_form.options'   => array(
        'module' => $this->getDmModule(),
        'query'  => $this->getDmModule()->getTable()->createQuery('r')->orderBy('r.position asc')
		)
		));

		$this->form = $this->getService('admin_sort_table_form');

		$this->processSortForm($this->form);
	}

	public function executeSortTree(sfWebRequest $request)
	{
		$this->forward404Unless($this->getDmModule()->getTable()->isNestedSet());

		sfConfig::set('dm_pageBar_enabled', false);

		$this->context->getServiceContainer()->addParameters(array(
      'model_tree_view.defaults'  => array(),
      'model_tree_view.options'   => array(
        'model'  => $this->getDmModule()->getModel(),
        'module' => $this->getDmModule()->__toString()
		)
		));

		$this->dm_module = $this->getDmModule();

		$this->tree = $this->getService('model_tree_view', 'dmAdminModelTreeView');

	}

	public function executeSortReferers(sfWebRequest $request)
	{
		$this->forward404Unless($record = $this->getDmModule()->getTable()->find($request->getParameter('id')));

		$this->forward404Unless($moduleKey = $request->getParameter('refererModule'));

		$this->forward404Unless($this->context->getModuleManager()->hasModule($moduleKey));

		$refererModule = $this->context->getModuleManager()->getModule($moduleKey);

		$this->forward404Unless($refererModule->getTable()->isSortable());

		$this->getServiceContainer()->addParameters(array(
      'admin_sort_form.defaults'  => array(),
      'admin_sort_form.options'   => array(
        'module'        => $refererModule,
        'parentRecord'  => $record,
        'query'         => $this->getSortReferersQuery($refererModule)
		)
		));

		$this->form = $this->getService('admin_sort_referers_form');

		$this->processSortForm($this->form);
	}

	protected function getSortReferersQuery(dmModule $refererModule)
	{
		return $refererModule->getTable()->createQuery('r')
		->whereIsActive(true, $refererModule->getModel())
		->orderBy('r.position asc');
	}

	/*
	 * List elements sorting
	 */
	protected function addSortQuery($query)
	{
		if (array(null, null) == ($sort = $this->getSort()))
		{
			return;
		}

		if (!in_array(strtolower($sort[1]), array('asc', 'desc')))
		{
			$sort[1] = 'asc';
		}

		$this->tryToSortWithForeignColumn($query, $sort);
	}

	protected function getSort()
	{
		if (null !== $sort = $this->getUser()->getAttribute($this->getSfModule().'.sort', null, 'admin_module'))
		{
			return $sort;
		}

		$this->setSort($this->configuration->getDefaultSort());

		return $this->getUser()->getAttribute($this->getSfModule().'.sort', null, 'admin_module');
	}

	protected function setSort(array $sort)
	{
		if (null !== $sort[0] && null === $sort[1])
		{
			$sort[1] = 'asc';
		}

		$this->getUser()->setAttribute($this->getSfModule().'.sort', $sort, 'admin_module');
	}

	protected function isValidSortColumn($column)
	{
		return $this->getDmModule()->getTable()->hasField($column);
	}

	/*
	 * Batch actions
	 */
	public function executeBatch(sfWebRequest $request)
	{
		if (!$ids = $request->getParameter('ids'))
		{
			$this->getUser()->setFlash('error', 'You must at least select one item.');

			$this->redirect('@'.$this->getDmModule()->getUnderscore());
		}

		foreach($request->getParameterHolder()->getAll() as $key => $value)
		{
			if (strncmp($key, 'batch', 5) === 0)
			{
				$action = $key;
				break;
			}
		}

		if (!isset($action))
		{
			$this->getUser()->setFlash('error', 'You must select an action to execute on the selected items.');

			$this->redirect('@'.$this->getDmModule()->getUnderscore());
		}

		if (!method_exists($this, $method = 'execute'.ucfirst($action)))
		{
			throw new InvalidArgumentException(sprintf('You must create a "%s" method for action "%s"', $method, $action));
		}

		if (!$this->getUser()->hasCredential($this->configuration->getCredentials($action)))
		{
			$this->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));
		}

		$validator = new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => $this->getDmModule()->getModel()));
		try
		{
			// validate ids
			$ids = $validator->clean($ids);

			//@todo add record security check here ! using dmModuleSecurityManager, create new method in it
			//if there are some ids for which user can't execute the method, remove them, then execute them
			//something like
			$_action = substr($action, 5);
			$_action = dmString::strtolower($_action);
			$authorizedIdsForAction = $this->getDmModule()->getSecurityManager()->getIdsForAuthorizedActionWithinIds($_action, $this->getUser()->getUser(), $ids);
			if(!$authorizedIdsForAction){
				throw new LogicException('You cannot delete those elements because you are not authorized to do so.', 401);
			}
			if(count($ids) != count($authorizedIdsForAction)){
				$request->setParameter('excluded_ids', array_diff($ids, $authorizedIdsForAction));
			}
			$request->setParameter('ids', $authorizedIdsForAction);
			// execute batch
			$this->$method($request);
		}
		catch (sfValidatorError $e)
		{
			$this->getUser()->setFlash('error', 'A problem occurs when deleting the selected items as some items do not exist anymore. ');
		}
		catch (LogicException $e)
		{
			$this->getUser()->setFlash('error', $e->getMessage());
		}

		$this->redirect('@'.$this->getDmModule()->getUnderscore());
	}

	protected function executeBatchDelete(sfWebRequest $request)
	{
		$table = $this->getDmModule()->getTable();
		$ids = $request->getParameter('ids');

		foreach($table->createQuery()->whereIn($table->getPrimaryKey(), $ids)->fetchRecords() as $record)
		{
			$record->notify('delete');
		}

		$count = $table->createQuery()
		->delete()
		->from($this->getDmModule()->getModel())
		->whereIn($table->getPrimaryKey(), $ids)
		->execute();

		if($request->hasParameter('excluded_ids') && $count >= count($ids))
		{
			$this->getUser()->logInfo('All the items you are allowed to delete have been successfully deleted. Other items were not deleted because you are not authorized to do so.');
		}
		elseif($request->hasParameter('excluded_ids') && $count < count($ids))
		{
			$this->getUser()->logInfo('An error occurs. You are authorized to delete only some of the selected items. Some of the items you are authorized to delete have been deleted.');
		}
		elseif ($count >= count($ids))
		{
			$this->getUser()->logInfo('The selected items have been deleted successfully.');
		}
		else
		{
			$this->getUser()->logInfo('A problem occurs when deleting the selected items.');
		}

		$this->redirect('@'.$this->getDmModule()->getUnderscore());
	}

	protected function executeBatchActivate(sfWebRequest $request)
	{
		$this->batchToggleBoolean((array) $request->getParameter('ids'), 'is_active', true);

		$this->redirect('@'.$this->getDmModule()->getUnderscore());
	}

	protected function executeBatchDeactivate(sfWebRequest $request)
	{
		$this->batchToggleBoolean((array) $request->getParameter('ids'), 'is_active', false);

		$this->redirect('@'.$this->getDmModule()->getUnderscore());
	}

	public function executeToggleBoolean(dmWebRequest $request)
	{
		$this->forward404Unless(
		$this->getDmModule()->getTable()->hasField($field = $request->getParameter('field'))
		&& ($record = $this->getDmModule()->getTable()->find($request->getParameter('pk')))
		);

		if('is_active' === $field && $record->getDmModule()->hasPage() && ($page = $record->getDmPage()))
		{
			$page->setIsActiveManually(!$record->get($field))->save();
		} else {
			$record->set($field, !$record->get($field));
			$record->save();
		}

		$this->getDispatcher()->notify(new sfEvent($this, 'dm.controller.redirect'));

		return $this->renderText($record->$field ? '1' : '0');
	}

	/**
	 * @param dmDoctrineQuery $query
	 * @return dmDoctrineQuery
	 */
	protected function addRecordPermissionQuery($query)
	{
		$user = $this->getUser()->getUser();
		if($user && $user->get('is_super_admin')){
			return;
		}

		if($this->getDmModule()->getSecurityManager()->isActionStrategicalySecurized($this->actionName))
		{
			return $this->getDmModule()->getSecurityManager()->getActionSecurizationStrategy($this->actionName)->addPermissionCheckToQuery($query, $this->actionName, $this->moduleName);
		}
		return $query;
	}

	/**
	 * Builds a query to request the object.
	 * This function can be overloaded to better fit your needs
	 * (i.e. make only one query against db to fetch every needed bits
	 * for your action & templates).
	 *
	 * @param unknown_type $pk the primary key for query
	 * @param unknown_type $relations the relations to leftJoin, aliases
	 * @param unknown_type $locals the local keys to join
	 * @param unknown_type $noBuilder if you want to let code call configuration method if exists
	 * @return dmDoctrineQuery
	 */
	public function buildObjectQuery($pk, $relations = array(), $locals = array(), $noBuilder = false)
	{
		//$fieldsets = $this->configuration->getFormFields($this->form, $this->actionName);
		$method = 'buildObjectQueryFor' . dmString::camelize($this->actionName);
		if(!$noBuilder && method_exists($this->configuration, $method))
		{
			return $this->configuration->$method($pk, $relations, $locals, $this);
		}
		else
		{
			$table = $this->getDmModule()->getTable();
			$id = $table->getIdentifier();
			if(is_array($id)) { $id = $id[0]; }
			$query = $table->createQuery('o')->where('o.' . $id . ' = ?', $pk);

			$table->joinLocals($query, true, $locals);
			$table->joinRelations($query, $relations, true);
			return $query;
		}
	}

	/**
	 * Returns an array of strings representing the relations alias
	 * @return array
	 */
	protected function getRelationsAlias()
	{
		$method = 'get' . ucfirst($this->actionName === 'index' ? 'list' : $this->actionName) . 'Display';
		if(!method_exists($this->configuration, $method))
		{
			$method = 'getFormDisplay';
		}
		$fieldsets = $this->configuration->$method();
		switch($this->actionName)
		{
			case 'filter':
				$fieldsets = $this->configuration->getFilterDisplay();
				break;
			case 'index':
				break;
			default:
				$fieldsets = array_merge($fieldsets, $this->configuration->getFormDisplay());
				break;
		}
		$relations = array();

		switch($this->actionName)
		{
			case 'filter':
				return $this->doGetRelations($fieldsets);
				break;
			default:
				foreach($fieldsets as $fieldset=>$fields)
				{
					if(!is_array($fields)){
						return $this->doGetRelations($fieldsets);
					}else{
						$relations = array_merge($relations, $this->doGetRelations($fields));
					}
				}
				break;
		}
		return array_unique(array_diff($relations, array_keys($this->getDmModule()->getTable()->getRelationHolder()->getLocals())));
	}

	/**
	 * Called by getRelations() as it uses $this->configuration get*Fields()
	 * which can be an array of array or an array of string.
	 * This function helps no repeating code.
	 *
	 * @param array $fields
	 */
	protected function doGetRelations($fields)
	{
		$relations = array();
		foreach($fields as $field)
		{
			$relationName = false;
			if(substr($field, strlen($field)-5, strlen($field)) === '_list'){
				$relationName = substr($field, 0, strlen($field)-5);
			}elseif(substr($field, 0, 1) === '_'){
				$relationName = substr($field, 1, strlen($field) -1);
			}
			if($relationName)
			{
				$field = dmString::camelize($relationName);
				if( $this->getDmModule()->getTable()->hasRelation($field))
				{
					//don't add Doctrine_Relation_LocalKey !
					if(!$this->getDmModule()->getTable()->getRelation($field) instanceof Doctrine_Relation_LocalKey)
					{
						$relations[] = $field;
					}
				}
			}
		}
		return $relations;
	}

	/**
	 * Called via ajax by edit forms which have many-to-many widgets
	 *
	 * @param dmWebRequest $request
	 */
	public function executePaginateRelation(dmWebRequest $request)
	{
		$field = dmString::lcfirst($request->getParameter('field'));
		$relation = dmString::camelize(substr($field, 0, strlen($field) -5)); //remove _list @todo make it given by $request, using .metadata() and writting it within template
		$table = $this->getDmModule()->getTable();

		if($table->hasRelation($relation))
		{
			$relation = $table->getRelation($relation);
		}elseif($table->hasRelation($field)){
			$relation = $table->getRelation($field);
		}elseif($table->hasRelation($relation = dmString::camelize($field)))
		{
			$relation = $table->getRelation($relation);
		}

		$startPage = $request->getParameter('page');
		$maxPerPage = $request->getParameter('maxPerPage');
		$this->getUser()->setAttribute($this->getModuleName() . '.' . $field . '.max_per_page', $maxPerPage, 'admin_module');
		$search = $request->getParameter('search', false);

		$model = dmString::tableize($this->getDmModule()->getModel());
		$this->$model = $this->getObject();

		$this->nearRecords = array();
		$this->form = $this->configuration->getForm($this->$model);
		$this->field = $this->form->getWidget($field);
		/**
		 * @var dmDoctrinePager
		 */
		$pager = $this->field->getPager();
		$pager->setMaxPerPage($maxPerPage);
		$pager->setPage($startPage);

		$query = $this->createPaginateRelationQuery($field, $relation, $search);
		$pager->setQuery($query);

		$this->name = $field;
		$fields = array_diff(array_keys($this->form->getWidgetSchema()->getFields()), array($field));
		foreach($fields as $field)
		{
			unset($this->form[$field]);
		}
		$this->configuration->setFormDisplay(array($this->name));
		$this->setTemplate('edit');
		$this->setLayout(false);
	}

	protected function createPaginateRelationQuery($field, $relation, $search)
	{
		$queryBuilder = 'buildQueryFor' . dmString::camelize($field);
		if(!method_exists($this, $queryBuilder))
		{
			$table = dmDb::table($relation['class']);
			$query = $table->createQuery('r');
			if(strlen($search)>0){
				$this->processSearchQuery($query, $search, $table);
			}
			$this->search = $search;
		}else{
			$query = $this->$queryBuilder($search);
		}
		return $query;
	}
}