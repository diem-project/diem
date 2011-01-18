<?php

class dmDoctrineTruncateTablesTaskTask extends dmContextTask
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
		$this->logSection('doctrine', 'truncating tables');
		$con = $this->withDatabase();
		$dbh = Doctrine_Manager::getInstance()->getCurrentConnection()->getDbh();
		$dbh->query('SET foreign_key_checks = 0');

		$tables = $dbh->query('SHOW TABLES');

		foreach($tables as $table)
		{
			$dbh->query('TRUNCATE ' . $table[0]);
		}
	}
}
