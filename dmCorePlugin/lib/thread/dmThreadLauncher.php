<?php

class dmThreadLauncher extends dmConfigurable
{
  protected
  $filesystem;
  
  public function __construct(dmFilesystem $filesystem, array $options = array())
  {
    $this->filesystem = $filesystem;
    
    $this->initialize($options);
  }
  
  public function execute($threadClass, array $threadOptions = array())
  {
    if( !isset($threadOptions['culture']))
    {
      $threadOptions['culture'] = dmDoctrineRecord::getDefaultCulture();
    }
    
    $command = $this->getCommand($threadClass, $threadOptions);
    
    if (!$this->filesystem->exec($command))
    {
      throw new dmThreadException(sprintf(
        "Thread %s failed ( app: %s, env: %s )\ncommand : %s\nmessage : %s",
        $threadClass,
        $this->options['app'],
        $this->options['env'],
        $this->getLastExec('command'),
        $this->getLastExec('output')
      ));
    }
  }
  
  public function getCommand($threadClass, array $threadOptions = array())
  {
    return sprintf('%s "%s" %s "%s"',
      sfToolkit::getPhpCli(),
      $this->getCliFileFullPath(),
      $threadClass,
      str_replace('"', '\\"', serialize($threadOptions))
    );
  }
  
  public function getCliFileFullPath()
  {
    return dmProject::rootify($this->getOption('cli_file'));
  }
  
  public function getLastExec($name = null)
  {
    return $this->filesystem->getLastExec($name);
  }
  
  protected function initialize($options)
  {
    $this->configure($options);
    
    $this->checkCliFile();
  }
  
  public function getDefaultOptions()
  {
    return array(
      'app'       => sfConfig::get('sf_app'),
      'env'       => sfConfig::get('sf_environment'),
      'debug'     => sfConfig::get('sf_debug'),
      'cli_file'  => 'cache/dm/cli.php'
    );
  }
  
  protected function checkCliFile()
  {
    $file = $this->getCliFileFullPath();
    
    if (!file_exists($file) || file_get_contents($file) != $this->getCliFileContent())
    {
      $this->filesystem->mkdir(dirname($file));
      
      file_put_contents($file, $this->getCliFileContent());
    }
    
    if (!is_executable($file))
    {
      chmod($file, 0777);
    }
    
    if (!is_executable($file) && '/' === DIRECTORY_SEPARATOR)
    {
      throw new dmException('Can not make '.dmProject::unRootify($file).' executable');
    }
  }
  
  protected function getCliFileContent()
  {
    return "<?php

require_once('".sfConfig::get('sf_root_dir')."/config/ProjectConfiguration.class.php');

\$configuration = ProjectConfiguration::getApplicationConfiguration('{$this->options['app']}', '{$this->options['env']}', ".($this->options['debug'] ? 'true' : 'false').", '".sfConfig::get('sf_root_dir')."');

\$threadClass = \$argv[1];
\$threadOptions = unserialize(\$argv[2]);

\$thread = new \$threadClass(\$configuration, \$threadOptions);

\$thread->execute();

return 0;";
  }
}