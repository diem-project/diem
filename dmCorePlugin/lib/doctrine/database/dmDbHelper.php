<?php
class dmDbHelper
{

	/**
	 * @var Doctrine_Connection
	 */
	protected $conn;

	/**
	 * @var string
	 */
	protected $driver;

	/**
	 * @param Doctrine_Connection $conn
	 */
	public function __construct($conn, $dispatcher = null, $formatter = null, $log = false)
	{
		$this->log = $log;
		$this->conn= $conn;
		$this->dispatcher = $dispatcher;
		$this->formatter = $formatter;
	}

	protected function getConnection($conn = null)
	{
		if(null === $conn)
		{
			$conn = $this->conn;
		}
		return $conn;
	}

	/**
	 * Returns tables of $conn
	 *  @param Doctrine_Connection $conn
	 */
	public function getTables($conn = null)
	{
		$conn = $this->getConnection($conn);
		switch($conn->getDriverName())
		{
			case 'Sqlite':
				$sql = 'select name from sqlite_master where type=\'table\';';
				break;
			default:
				$sql = 'SHOW TABLES';
				break;
		}
		return $conn->getDbh()->query($sql);
	}

	/**
	 * Drop tables
	 * @param boolean $disableForeignKeyChecks
	 * @param Doctrine_Connection $conn
	 */
	public function dropTables($disableForeignKeyChecks = true, $conn = null)
	{
		$conn = $this->getConnection($conn);
		if($disableForeignKeyChecks)
		{
			$this->enableForeignKeyChecks(false);
		}
		foreach($this->getTables($conn) as $table)
		{
			$conn->getDbh()->query('DROP TABLE ' . $table[0]);
		}
		if($disableForeignKeyChecks)
		{
			$this->enableForeignKeyChecks(true);
		}
	}


	/**
	 * Emtpy tables by deleting rows
	 * @param boolean $disableForeignKeyChecks
	 * @param Doctrine_Connection $conn
	 */
	public function truncateTables($disableForeignKeyChecks = true, $conn = null)
	{
		$conn = $this->getConnection($conn);
		switch($conn->getDriverName())
		{
			case 'Sqlite':
				$statement = 'DELETE FROM %s;';
				break;
			default:
				$statement = 'TRUNCATE %s';
				break;
		}

		if($disableForeignKeyChecks)
		{
			$this->enableForeignKeyChecks(false);
		}
		foreach($this->getTables($conn) as $table)
		{
			$this->logSection('sql', sprintf($statement, $table[0]));
			$conn->getDbh()->query(sprintf($statement, $table[0]));
		}
		if($disableForeignKeyChecks)
		{
			$this->enableForeignKeyChecks(true);
		}

	}

	/**
	 * Enable or disable foreign key checks for $conn
	 * 
	 * @param boolean $true disable or enable ?
	 * @param Doctrine_Connection $conn
	 */
	public function enableForeignKeyChecks($true, $conn = null)
	{
		$conn = $this->getConnection($conn);
		switch($conn->getDriverName())
		{
			case 'Sqlite':
				$sql = 'PRAGMA foreign_keys = ' . ($true ? 'ON' : 'OFF');
				break;
			default:
				$sql = 'SET @@foreign_key_checks = ' . ($true ? '1' : '0');
				break;
		}
		$this->logSection('sql', $sql);
		$conn->getDbh()->query($sql);
	}

	
	protected function logSection($section, $message, $size = null, $style = 'INFO')
	{
		if($this->log)
		{
			$this->dispatcher->notify(new sfEvent($this, 'command.log', array($this->formatter->formatSection($section, $message, $size, $style))));
		}
	}
}