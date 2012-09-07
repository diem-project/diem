<?php

/**
 * Install Diem
 */
class dmI18nTask extends dmContextTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    parent::configure();
    
    $this->addArguments(array(
      new sfCommandArgument('application', sfCommandArgument::REQUIRED, 'The application name'),
      new sfCommandArgument('culture', sfCommandArgument::REQUIRED, 'The target culture'),
    ));

    $this->addOptions(array(
      new sfCommandOption('display-new', null, sfCommandOption::PARAMETER_NONE, 'Output all new found strings'),
      new sfCommandOption('display-old', null, sfCommandOption::PARAMETER_NONE, 'Output all old strings'),
      new sfCommandOption('auto-save', null, sfCommandOption::PARAMETER_NONE, 'Save the new strings'),
      new sfCommandOption('auto-delete', null, sfCommandOption::PARAMETER_NONE, 'Delete old strings'),
    ));

    $this->namespace = 'dm';
    $this->name = 'i18n';
    $this->briefDescription = 'Extracts i18n strings from php files';

    $this->detailedDescription = <<<EOF
The [i18n:extract|INFO] task extracts i18n strings from your project files
for the given application and target culture:

  [./symfony i18n:extract frontend fr|INFO]

By default, the task only displays the number of new and old strings
it found in the current project.

If you want to display the new strings, use the [--display-new|COMMENT] option:

  [./symfony i18n:extract --display-new frontend fr|INFO]

To save them in the i18n message catalogue, use the [--auto-save|COMMENT] option:

  [./symfony i18n:extract --auto-save frontend fr|INFO]

If you want to display strings that are present in the i18n messages
catalogue but are not found in the application, use the 
[--display-old|COMMENT] option:

  [./symfony i18n:extract --display-old frontend fr|INFO]

To automatically delete old strings, use the [--auto-delete|COMMENT] but
be careful, especially if you have translations for plugins as they will
appear as old strings but they are not:

  [./symfony i18n:extract --auto-delete frontend fr|INFO]
EOF;

  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $this->withDatabase();
    
    // clear options non applicable for sfI18n extract task
    unset($options['application'], $options['env'], $arguments['task']);

    $this->runTask('i18n:extract', $arguments, $options);
  }
  
}
