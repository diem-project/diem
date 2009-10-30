<?php

class dmDoctrineBuildFiltersTask extends sfDoctrineBuildFiltersTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    parent::configure();
    $this->aliases = array();
    $this->namespace = 'dm';
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $this->logSection('doctrine', 'generating filter form classes');
    $databaseManager = new sfDatabaseManager($this->configuration);
    $generatorManager = new sfGeneratorManager($this->configuration);
    $generatorManager->generate('dmDoctrineFormFilterGenerator', array(
      'model_dir_name'  => $options['model-dir-name'],
      'filter_dir_name' => $options['filter-dir-name'],
    ));

    $properties = parse_ini_file(sfConfig::get('sf_config_dir').DIRECTORY_SEPARATOR.'properties.ini', true);

    $constants = array(
      'PROJECT_NAME' => isset($properties['symfony']['name']) ? $properties['symfony']['name'] : 'symfony',
      'AUTHOR_NAME'  => isset($properties['symfony']['author']) ? $properties['symfony']['author'] : 'Your name here'
    );

    // customize php and yml files
    $finder = sfFinder::type('file')->name('*.php');
    $quietFilesystem = new sfFilesystem;
    $quietFilesystem->replaceTokens($finder->in(sfConfig::get('sf_lib_dir').'/filter/'), '##', '##', $constants);

    $this->reloadAutoload();
  }
}