<?php

class dmDoctrineDataLoadTask extends dmContextTask
{
	protected function configure()
	{
		parent::configure();
		 
		$this->addArguments(array(
		new sfCommandArgument('dir_or_file', sfCommandArgument::OPTIONAL | sfCommandArgument::IS_ARRAY, 'Directory or file to load'),
		));
		 
		$this->addOptions(array(
		new sfCommandOption('append', 'a', sfCommandOption::PARAMETER_NONE, 'Don\'t delete current data in the database'),
		new sfCommandOption('no-integrity', 'n', sfCommandOption::PARAMETER_NONE, 'Disable integrity checks'),
		));

		$this->namespace        = 'dm';
		$this->name             = 'data-load';
		$this->briefDescription = '';
		$this->detailedDescription = <<<EOF
The [dmDoctrineDataLoad|INFO] task does load doctrine fixtures in the Diem context
Call it with:

  [php symfony dmDoctrineDataLoad|INFO]
EOF;
	}

	protected function execute($arguments = array(), $options = array())
	{
		$this->withDatabase();
		if(isset($arguments['task'])) {
			unset($arguments['task']);
		}
		$noIntegrity = $options['no-integrity'];
		unset($options['no-integrity']);
		
		if($noIntegrity)
		{
			$dbh = Doctrine_Manager::getInstance()->getCurrentConnection()->getDbh();
			$dbh->query('SET foreign_key_checks = 0');
		}
		$this->runTask('doctrine:data-load', $arguments, $options);
		if($noIntegrity)
		{
			$dbh->query('SET foreign_key_checks = 1');
		}
	}
}
