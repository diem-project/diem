<?php
  
sfConfig::set('dm_core_dir', realpath(dirname(__FILE__).'/..'));

require_once(sfConfig::get('dm_core_dir').'/lib/core/dm.php');
require_once(sfConfig::get('dm_core_dir').'/lib/basic/dmString.php');
require_once(sfConfig::get('dm_core_dir').'/lib/os/dmOs.php');
require_once(sfConfig::get('dm_core_dir').'/lib/project/dmProject.php');
require_once(sfConfig::get('dm_core_dir').'/lib/task/dmServerCheckTask.class.php');

if ('/' !== DIRECTORY_SEPARATOR)
{
  $this->logBlock('Sorry, but Diem 5 only run on Unix servers.', 'ERROR_LARGE');
  exit;
}

$this->logBlock('Diem '.DIEM_VERSION.' installer', 'INFO_LARGE');

$this->logSection('Diem', 'Welcome in the Diem installation wizard. We will now check that your server matches Symfony 1.3 & Diem 5 requirements.');
$this->askConfirmation('Press ENTER');

$serverCheck = new dmServerCheckTask($this->dispatcher, $this->formatter);
$serverCheck->setCommandApplication($this->commandApplication);
$serverCheck->setConfiguration($config);

try
{
  $serverCheck->run();
}
catch(dmServerCheckException $e)
{
  if (!$this->askConfirmation('Do you want to continue the installation ? (y/n)'))
  {
    $this->log('Aborted.');
    exit;
  }
}

/*
 * INITIALIZATION
 */
$settings = array();

if ('Doctrine' != $this->options['orm'])
{
  throw new Exception('Sorry, Diem '.DIEM_VERSION.' only support the Doctrine orm');
}
  
$projectKey = dmProject::getKey();

/*
 * QUESTIONS
 */

$this->logSection($projectKey, 'Please answer a few questions to configure the '.$projectKey.' project'."\n");

$webDirName = $this->askAndValidate('Choose a web directory name ( default: web )', new sfValidatorAnd(array(
  new sfValidatorRegex(
    array('pattern' => '/^[\w\d-]+$/'),
    array('invalid' => 'Web dir must contain only alphanumeric characters')
  ),
  new sfValidatorRegex(
    array('pattern' => '/^(apps|lib|config|data|cache|log|plugins|test)$/', 'must_match' => false),
    array('invalid' => 'This directory is already used')
  )
)));
$settings['web_dir_name'] = empty($webDirName) ? 'web' : $webDirName;

do
{
  $settings['database'] = array(
    'name' => $this->ask('What is the database name ? ( default : '.dmString::underscore($projectKey).' )', 'QUESTION', dmString::underscore($projectKey)),
    'host' => $this->ask('What is the database host ? ( default : localhost )', 'QUESTION', 'localhost'),
    'user' => $this->ask('What is the database user ?'),
    'password' => $this->ask('What is the database password ?')
  );
    
  $settings['database']['dsn'] = sprintf('mysql://%s:%s@%s/%s',
    $settings['database']['user'], $settings['database']['password'], $settings['database']['host'], $settings['database']['name']
  );
  
  try
  {
    $dbh = new PDO($settings['database']['dsn'], $settings['database']['user'], $settings['database']['password']);
    $isDatabaseOk = true;
  }
  catch (PDOException $e)
  {
    $isDatabaseOk = false;
    $this->logBlock('The database configuration looks wrong. PDO says : '.$e->getMessage(), 'ERROR_LARGE');
    $this->log('');
  }
}
while(!$isDatabaseOk);

/*
 * APPLY
 */

$this->logBlock('Your configuration is valid', 'INFO_LARGE');

$confirmationMessage = sprintf('Are you ready to create the %s project ? This will erase the %s database'.' (Y/n)',
  dmProject::getKey(),
  $settings['database']['name']
);

if (!$this->askConfirmation($confirmationMessage))
{
  $this->log('Aborting.');
  exit;
}

$this->filesystem->mirror(
  dmOs::join(sfConfig::get('dm_core_dir'), 'data/skeleton'),
  sfConfig::get('sf_root_dir'),
  sfFinder::type('any')->discard('.sf'),
  array('override' => true)
);

$this->replaceTokens(sfConfig::get('sf_config_dir'), array(
  'SYMFONY_CORE_AUTOLOAD' => $symfonyCoreAutoload,
  'DIEM_CORE_STARTER' => var_export(dmOs::join(sfConfig::get('dm_core_dir'), 'lib/core/dm.php'), true),
  'DIEM_WEB_DIR_NAME' => var_export($settings['web_dir_name'], true)
));

$this->replaceTokens(sfConfig::get('sf_test_dir'), array(
  'DIEM_PROJECT_PASSWORD' => var_export($settings['database']['password'], true)
));
    
$this->filesystem->remove(array(
  dmProject::rootify('web/css'),
  dmProject::rootify('web/css/main.css'),
  dmProject::rootify('web/images'),
  dmProject::rootify('data/fixtures'),
  dmProject::rootify('data/fixtures/fixtures.yml')
));

if ('web' != $settings['web_dir_name'])
{
  $this->filesystem->rename(dmProject::rootify('web'), dmProject::rootify($settings['web_dir_name']));
}

$db = $settings['database'];
$this->runTask('configure:database', array(
  'dsn' => $db['dsn'],
  'username' => $db['user'],
  'password' => $db['password']
));

/*
 * Let's fly
 */

dm::start();
require_once(dmProject::rootify('config/dmInstallerProjectConfiguration.class.php'));
$config = dmInstallerProjectConfiguration::activate(sfConfig::get('sf_root_dir'), $this->dispatcher);
$this->filesystem->remove(dmProject::rootify('config/dmInstallerProjectConfiguration.class.php'));

sfConfig::set('sf_debug', true);

sfConfig::set('sf_error_reporting', (E_ALL | E_STRICT));

$task = $this->createTask('doctrine:build');
$task->setConfiguration($config);
$task->run(array(), array('all' => true, 'no-confirmation' => true));

$superAdmin = dmDb::create('DmUser', array(
  'is_super_admin' => true,
  'username' => 'admin',
  'password' => $settings['database']['password'],
  'email' => 'admin@'.dmProject::getKey().'.com'
))->saveGet();

// fix permission for common directories
$fixPerms = new dmProjectPermissionsTask($this->dispatcher, $this->formatter);
$fixPerms->setCommandApplication($this->commandApplication);
$fixPerms->setConfiguration($config);
$fixPerms->run();

// fix permission for common directories
$fixPerms = new dmPublishAssetsTask($this->dispatcher, $this->formatter);
$fixPerms->setCommandApplication($this->commandApplication);
$fixPerms->setConfiguration($config);
$fixPerms->run();

$this->logBlock('Cool ! Everything went fine', 'INFO_LARGE');

$this->logBlock('There is a last thing to do. Please run : php symfony dm:setup', 'INFO_LARGE');

$this->log('');

exit;