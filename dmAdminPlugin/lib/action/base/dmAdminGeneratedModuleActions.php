<?php

class dmAdminBaseGeneratedModuleActions extends dmAdminBaseActions
{
  /*
   * When sorting by a localKey column ( ex: categ_id ),
   * try to sort with foreign's table identifier column ( ex: categ.name )
   */
  protected function tryToSortWithForeignColumn(Doctrine_Query $query, array $sort)
  {
    // If the sort column is a local key, try to sort with foreign table
    if ($relation = $this->getDmModule()->getTable()->getRelationHolder()->getLocalByColumnName($sort[0]))
    {
      if ($relation instanceof Doctrine_Relation_LocalKey && ($foreignTable = $relation->getTable()) instanceof dmDoctrineTable)
      {
        if (($foreignColumn = $foreignTable->getIdentifierColumnName()) != 'id')
        {
          try
          {
            $query->addOrderBy(sprintf('%s.%s %s', $relation->getAlias(), $foreignColumn, $sort[1]));
            
            // Success !
            return;
          }
          catch(Exception $e)
          {
            $this->context->getLogger()->log(sprintf('Can not sort with %s.%s because its alias in query is not %s',
              $relation->getClass(), $foreignColumn, $relation->getAlias
            ));
          }
        }
      }
    }
    
    $query->addOrderBy($sort[0] . ' ' . $sort[1]);
  }
  
  protected function processSortForm($form)
  {
    if ($this->getRequest()->isMethod('post'))
    {
      $form->bind();
      
      if($form->isValid())
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
          
          $this->getUser()->logError($this->context->getI18n()->__('A problem occured when sorting the items'), true);
        }

        $this->getUser()->logInfo($this->context->getI18n()->__('The items have been sorted successfully'), true);
        
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
    
    $count = myDoctrineQuery::create()
      ->update($table->getComponentName())
      ->whereIn($pk, $ids)
      ->andWhere($field.' = ?', 1-$value)
      ->set($field, $value)
      ->execute();
      
    if ($count)
    {
      $this->getDmContext()->getPageTreeWatcher()->addModifiedTable($table);
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
    $export = new $exportClass($options['module']->getTable());
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
      'filename' => sprintf('%s-%s_%s.%s',
        dmConfig::get('site_name'),
        dm::getI18n()->__($options['module']->getName()),
        date('Y-m-d'),
        $options['extension']
      ),
      'type' => sprintf('%s; charset=%s', $mime, $options['encoding'])
    ));
  }
}