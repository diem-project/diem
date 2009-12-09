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

$this->logSection('Diem', 'Welcome into the Diem installation wizard.');
$this->logSection('Diem', 'We will now check that your server matches Symfony '.SYMFONY_VERSION.' and Diem 5.0 requirements.');

usleep(1000000);
$this->askConfirmation('Press ENTER');

$serverCheck = new dmServerCheckTask($this->dispatcher, $this->formatter);
$serverCheck->setCommandApplication($this->commandApplication);

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

$culture = $this->askAndValidate('Choose your site main language ( default: en )', new sfValidatorRegex(
  array('pattern' => '/^[\w\d-]+$/', 'max_length' => 2, 'min_length' => 2, 'required' => false),
  array('invalid' => 'Language must contain two alphanumeric characters')
));
$settings['culture'] = empty($culture) ? 'en' : $culture;

$webDirName = $this->askAndValidate('Choose a web directory name ( example: web, html, public_html )',
new sfValidatorAnd(array(
  new sfValidatorRegex(
    array('pattern' => '/^[\w\d-]+|$/'),
    array('invalid' => 'Web directory name must contain only alphanumeric characters')
  ),
  new sfValidatorRegex(
    array('pattern' => '/^(apps|lib|config|data|cache|log|plugins|test)$/', 'must_match' => false),
    array('invalid' => 'This directory is already used')
  )
)));
$settings['web_dir_name'] = empty($webDirName) ? 'web' : $webDirName;

do
{
  $defaultDbName = dmString::underscore(str_replace('-', '_', $projectKey));
  
  $settings['database'] = array(
    'db' => $this->ask('What kind of database will we used ? ( mysql | pgsql )', 'QUESTION', 'mysql'),
    'name' => $this->ask('What is the database name ? ( default : '.$defaultDbName.' )', 'QUESTION', $defaultDbName),
    'host' => $this->ask('What is the database host ? ( default : localhost )', 'QUESTION', 'localhost'),
    'user' => $this->ask('What is the database user ?'),
    'password' => $this->ask('What is the database password ?')
  );
    
  switch($settings['database']['db'])
  {
    case "mysql":
      $settings['database']['dsn'] = sprintf('mysql://%s:%s@%s/%s',
        $settings['database']['user'], $settings['database']['password'], $settings['database']['host'], $settings['database']['name']
      );
    break;
    case "pgsql":
      $settings['database']['dsn'] = sprintf('pgsql:host=%s;dbname=%s;user=%s;password=%s',
      $settings['database']['host'], $settings['database']['name'], $settings['database']['user'], $settings['database']['password']);
    break;
    default:
      $isDatabaseOk = false;
      $this->logBlock('Diem 5.0 only supports mysql and pgsql', 'ERROR_LARGE');
      $this->log('');
  }
  
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

usleep(1000000);

if (!$this->askConfirmation(array(
  'The installation will remove all data in the '.$settings['database']['name'].' database.',
  '',
  'Are you sure you want to proceed? (y/N)'
), 'QUESTION_LARGE', false)
)
{
  $this->logSection('diem', 'installation aborted');

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
  'DIEM_CORE_STARTER'     => var_export(dmOs::join(sfConfig::get('dm_core_dir'), 'lib/core/dm.php'), true),
  'DIEM_WEB_DIR'          => var_export(dmOs::join(sfConfig::get('sf_root_dir'), $settings['web_dir_name']), true),
  'DIEM_CULTURE'          => var_export($settings['culture'], true)
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

$this->logBlock('Installing '.$projectKey.'. This may take some time.', 'INFO_LARGE');

try
{
  $out = $err = null;
  $this->getFilesystem()->execute(sprintf(
    '%s "%s" %s',
    sfToolkit::getPhpCli(),
    sfConfig::get('sf_root_dir').'/symfony',
    'dm:setup --no-confirmation'
  ), $out, $err);
  
  $this->logBlock('Your project is now ready for web access. See you on admin_dev.php.', 'INFO_LARGE');
  $this->logBlock('Your login is admin and your password is '.(empty($settings['database']['password']) ? '"admin"' : 'the database password'), 'INFO_LARGE');
}
catch(Exception $e)
{
  $this->logBlock('There is a last thing to do. Please run : php symfony dm:setup', 'INFO_LARGE');
}

exit;