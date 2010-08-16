<?php
/**
 * dmMedia actions.
 *
 * @package    diem
 * @subpackage dmMedia
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class dmConsoleActions extends dmAdminBaseActions
{
  
  protected static
    $defaultCommands = 'sf man ll ls pwd cat mkdir rm cp mv touch chmod free df find clear php';

  protected function getCommands()
  {
    return explode(' ', sfConfig::get('dm_console_commands', self::$defaultCommands));
  }

  protected function getAliases()
  {
    return sfConfig::get('dm_console_aliases', array('ll' => 'ls -l'));
  }
    
  public function executeCommand(sfWebRequest $request)
  {
    $command = trim($request->getParameter("dm_command"));

    if (substr($command, 0, 2) == "sf")
    {
      $command = substr($command, 3);
      $exec = sprintf('%s "%s" %s --color', sfToolkit::getPhpCli(), dmProject::getRootDir().'/symfony', $command);
    }
    else
    {
      $options = substr(trim($command), 0, 2) == 'll' ||  substr(trim($command), 0, 2) == 'ls' ? '--color' : '' ;
      $parts = explode(" ", $command);
      $parts[0] = dmArray::get($this->getAliases(), $parts[0], $parts[0]);
      $command = implode(" ", $parts);
      $parts = explode(" ", $command);
      $command = dmArray::get($this->getAliases(), $command, $command);
      if (!in_array($parts[0], $this->getCommands()))
        return $this->renderText(sprintf(
          "%s<li>This command is not available. You can do: <strong>%s</strong></li>",
          $this->renderCommand($command), implode(' ', $this->getCommands())
        ));
      $exec = sprintf("%s $options", $command);
    }

    ob_start();
    passthru($exec.' 2>&1', $return);
    $raw = dmAnsiColorFormatHtmlRenderer::render(ob_get_clean());
    $arr = explode("\n", $raw);
    $res = $this->renderCommand($command);
    foreach($arr as $a)
      $res .= "<li class='dm_result_command'><pre>".$a."</pre></li>";
    return $this->renderText($res);
  }
  
  protected function renderCommand($command)
  {
    return '<li class="dm_command_user">'.$this->getUser()->getAttribute('dm_console_prompt').$command.'</li>';
  }

  public function executeIndex(sfWebRequest $request)
  {
    $this->commands = implode(' ', $this->getCommands());

    $this->uname = php_uname();

    $filesystem = $this->context->getFilesystem();
    
    if ($filesystem->exec('whoami'))
    {
      $this->whoami = $filesystem->getLastExec('output');
    }
    else
    {
      $this->whoami = 'unknown_user';
    }
    
    $this->pwd = getcwd();

    $this->prompt = $this->whoami.'@'.php_uname('n').':'.'~/'.dmProject::unRootify($this->pwd).'$&nbsp;';

    $this->getUser()->setAttribute('dm_console_prompt', $this->prompt);
    
    $this->form = $this->getCommandForm();
  }

  protected function getCommandForm()
  {
    $form = new BaseForm;
    
    $form->setWidgetSchema(new sfWidgetFormSchema(array(
      'dm_command' => new sfWidgetFormInputText
    )));
    
    $form->setValidatorSchema(new sfValidatorSchema(array(
      'dm_command' => new sfValidatorString
    )));
    
    return $form;
  }
  
}
