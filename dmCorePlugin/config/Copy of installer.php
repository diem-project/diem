<?php

$options['skip'] = true;

$installer = new dmInstaller($this, $arguments, $options);

$installer->execute();

class dmInstaller
{
  protected
  $task,
  $filesystem,
  $projectKey,
  $options,
  $settings = array();
  
  public function __construct(sfGenerateProjectTask $task, array $arguments, array $options)
  {
    $this->task = $task;
    $this->filesystem = $task->getFilesystem();
    
    $this->arguments = $arguments;
    $this->initialize($options);
  }
  
  protected function initialize(array $options)
  {
    $this->options = $options;
    
    sfConfig::set('dm_core_dir', realpath(dirname(__FILE__).'/..'));
    
    require_once(sfConfig::get('dm_core_dir').'/lib/core/dm.php');
    require_once(sfConfig::get('dm_core_dir').'/lib/basic/dmString.php');
    require_once(sfConfig::get('dm_core_dir').'/lib/os/dmOs.php');
    require_once(sfConfig::get('dm_core_dir').'/lib/project/dmProject.php');
    
    if ('Doctrine' != $this->options['orm'])
    {
      throw new Exception('Sorry, Diem '.DIEM_VERSION.' only support the Doctrine orm');
    }
    
    $this->projectKey = dmProject::getKey();
  }
  
  public function execute()
  {
    $this->logBlock(array('Diem '.DIEM_VERSION.' installer'), 'INFO_LARGE');
    
    $this->logSection($this->projectKey, 'Please answer a few questions to configure the '.$this->projectKey.' project'."\n");
    
    if (isset($this->options['skip']) && $this->options['skip'])
    {
      $this->settings = array(
        'web_dir_name' => 'web',
        'database' => array(
          'name' => 'diem_site',
          'host' => 'localhost',
          'user' => 'root',
          'password' => 'm'
        )
      );
    }
    else
    {
      $this->settings['web_dir_name'] = $this->chooseWebDirName();
      
      $this->settings['database'] = $this->chooseDatabase();
    }
    
    $this->apply();
  }
  
  protected function apply()
  {
    $this->filesystem->mirror(
      dmOs::join(sfConfig::get('dm_core_dir'), 'data/skeleton'),
      sfConfig::get('sf_root_dir'),
      sfFinder::type('any')->discard('.sf'),
      array('override' => true)
    );

    $symfonyCoreAutoload = 0 === strpos(sfConfig::get('sf_symfony_lib_dir'), sfConfig::get('sf_root_dir')) ?
      sprintf('dirname(__FILE__).\'/..%s/autoload/sfCoreAutoload.class.php\'', str_replace(sfConfig::get('sf_root_dir'), '', sfConfig::get('sf_symfony_lib_dir'))) :
      sfConfig::get('sf_symfony_lib_dir').'/autoload/sfCoreAutoload.class.php';

    $this->filesystem->replaceTokens(
      sfFinder::type('file')->in(sfConfig::get('sf_config_dir')), '##', '##',
        array(
        'SYMFONY_CORE_AUTOLOAD' => $symfonyCoreAutoload,
        'DIEM_CORE_STARTER' => dmOs::join(sfConfig::get('dm_core_dir'), 'lib/core/dm.php'),
        'DIEM_WEB_DIR_NAME' => $this->settings['web_dir_name']
      )
    );
    
    $this->filesystem->remove(dmProject::rootify('web/css/main.css'));
    $this->filesystem->remove(dmProject::rootify('web/css'));
    $this->filesystem->remove(dmProject::rootify('web/images'));
    $this->filesystem->remove(dmProject::rootify('data/fixtures/fixtures.yml'));
    $this->filesystem->remove(dmProject::rootify('data/fixtures'));
    
    if ('web' != $this->settings['web_dir_name'])
    {
      $this->filesystem->rename(dmProject::rootify('web'), dmProject::rootify($this->settings['web_dir_name']));
    }
    
    $db = $this->settings['database'];
    $this->runTask('sfDoctrineConfigureDatabaseTask', array(
      'dsn' => sprintf('mysql://%s:%s@%s/%s',
        $db['user'], $db['password'], $db['host'], $db['name']
      ),
      'username' => $db['user'],
      'password' => $db['password']
    ));
    
    $configuration = new dmProjectConfiguration(sfConfig::get('sf_root_dir'), $this->dispatcher);
    
    require_once(dmOs::join(sfConfig::get('dm_core_dir'), 'lib/core/dm.php'));
    dm::start();
    
//    require_once(dmOs::join(sfConfig::get('dm_core_dir'), 'lib/config/dmInlineAssetConfigHandler.php'));

//    sfSimpleAutoload::getInstance()->reload();
    
    $conf = new ProjectConfiguration();
    
    print_r(ProjectConfiguration::getActive());
    
    $this->runTask('sfDoctrineBuildModelTask');
  }
  
  protected function chooseDatabase()
  {
    return array(
      'name' => $this->chooseDatabaseName(),
      'host' => $this->chooseDatabaseHost(),
      'user' => $this->chooseDatabaseUser(),
      'password' => $this->chooseDatabasePassword()
    );
  }
  
  protected function chooseDatabaseName()
  {
    $default = dmString::underscore($this->projectKey);
    return $this->ask('What is the database name ? ( default : '.$default.' )', 'QUESTION', $default);
  }
  
  protected function chooseDatabaseUser()
  {
    return $this->ask('What is the database user ?');
  }
  
  protected function chooseDatabasePassword()
  {
    return $this->ask('What is the database password ?');
  }
  
  protected function chooseDatabaseHost()
  {
    return $this->ask('What is the database host ? ( default : localhost )', 'QUESTION', 'localhost');
  }

  protected function chooseWebDirName()
  {
    $webDirName = $this->askAndValidate('Choose a web directory name ( ex: web, public_html )', new sfValidatorAnd(array(
      new sfValidatorRegex(
        array('pattern' => '/^[\w\d-]+$/'),
        array('invalid' => 'Web dir must contain only alphanumeric characters')
      ),
      new sfValidatorRegex(
        array('pattern' => '/^(apps|lib|config|data|cache|log|plugins|test)$/', 'must_match' => false),
        array('invalid' => 'This directory is already used')
      )
    )));
    
    return $webDirName ? $webDirName : 'web';
  }
  
  protected function runTask($class, array $arguments = array(), array $options = array())
  {
    $task = new $class(new sfEventDispatcher, $this->getFormatter());
    
    return $task->run($arguments, $options);
  }
  
  public function __call($method, $params = array())
  {
    return call_user_func_array(array($this->task, $method), $params);
  }
  
  public function askAndValidate($question, sfValidatorBase $validator, array $options = array())
  {
    if (!is_array($question))
    {
      $question = array($question);
    }

    $options = array_merge(array(
      'value'    => null,
      'attempts' => false,
      'style'    => 'QUESTION',
    ), $options);

    // does the provided value passes the validator?
    if ($options['value'])
    {
      try
      {
        return $validator->clean($options['value']);
      }
      catch (sfValidatorError $error)
      {
      }
    }

    // no, ask the user for a valid user
    $error = null;
    while (false === $options['attempts'] || $options['attempts']--)
    {
      if (null !== $error)
      {
        $this->logBlock($error->getMessage(), 'ERROR');
      }

      $value = $this->ask($question, $options['style'], null);

      try
      {
        return $validator->clean($value);
      }
      catch (sfValidatorError $error)
      {
      }
    }

    throw $error;
  }
}