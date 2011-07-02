<?php

class dmDoctrineTruncateTablesTask extends dmContextTask
{
	protected function configure()
	{
		parent::configure();
		$this->namespace        = 'dm';
		$this->name             = 'truncate-tables';
		$this->briefDescription = 'Will empty all tables';
		$this->detailedDescription = <<<EOF
The [dmDoctrineTruncateTablesTask|INFO] task does empty all tables.
Call it with:

  [php symfony dmDoctrineTruncateTablesTask|INFO]
EOF;
	}

	protected function execute($arguments = array(), $options = array())
	{
		$this->withDatabase();
		$this->logSection('doctrine', 'truncating tables');
		
		
		$options['timer'] && $timer = $this->timerStart('truncate-tables');
		
		/**
		 * @var dmDbHelper
		 */
		$this->helper = new dmDbHelper(Doctrine_Manager::getInstance()->getCurrentConnection(), $this->dispatcher, $this->formatter);
		$this->helper->truncateTables(true);
		
		$options['timer'] && $this->logSection('time', sprintf('%s s', $timer->getElapsedTime()));
	}
}
