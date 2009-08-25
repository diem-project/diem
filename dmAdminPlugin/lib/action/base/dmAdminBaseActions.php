<?php

class dmAdminBaseActions extends dmBaseActions
{
  
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