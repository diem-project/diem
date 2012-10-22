<?php

/**
 * Drop Db tables
 */
class dmDoctrineDropTablesTask extends dmContextTask
{


	/**
	 * @see sfTask
	 */
	protected function configure()
	{
		parent::configure();

		$this->namespace = 'dm';
		$this->name = 'drop-tables';
		$this->briefDescription = 'Drop all the tables of the Db';

		$this->detailedDescription = <<<EOF
Will delete all the tables in the database
EOF;
	}

	/**
	 * @see sfTask
	 */
	protected function execute($arguments = array(), $options = array())
	{
		$this->withDatabase();
		$this->logSection('doctrine', 'dropping tables');

		$this->helper = new dmDbHelper(Doctrine_Manager::getInstance()->getCurrentConnection(), $this->dispatcher, $this->formatter);
		$this->helper->dropTables(true);
	}
}