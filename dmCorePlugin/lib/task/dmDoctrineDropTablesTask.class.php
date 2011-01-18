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
		$this->logSection('doctrine', 'dropping tables');
		$con = $this->withDatabase();
		$dbh = Doctrine_Manager::getInstance()->getCurrentConnection()->getDbh();
		$dbh->query('SET foreign_key_checks = 0');
		
		$tables = $dbh->query('SHOW TABLES');
		
		foreach($tables as $table)
		{
			$dbh->query('DROP TABLE ' . $table[0]);
		}
		$dbh->query('SET foreign_key_checks = 1');
	}
}