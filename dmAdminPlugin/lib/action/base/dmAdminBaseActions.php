<?php

class dmAdminBaseActions extends dmBaseActions
{
	
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
	      dm::getI18n()->__($this->getDmContext()->getSite()->name),
	      dm::getI18n()->__($options['module']->getName()),
	      date('Y-m-d'),
	      $options['extension']
	    ),
      'type' => sprintf('%s; charset=%s', $mime, $options['encoding'])
    ));
	}
	
}