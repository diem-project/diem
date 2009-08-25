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
    $defaultCommands = 'sf man ll ls pwd cat mkdir rm cp mv touch chmod free df find clear',
    $prompt = "&raquo;&nbsp;";

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
      $exec = sprintf('%s "%s" %s', sfToolkit::getPhpCli(), dmProject::getRootDir().'/symfony', $command);
    }
    else
    {
      $parts = explode(" ", $command);
      $parts[0] = dmArray::get($this->getAliases(), $parts[0], $parts[0]);
      $command = implode(" ", $parts);
      $parts = explode(" ", $command);
      $command = dmArray::get($this->getAliases(), $command, $command);
      if (!in_array($parts[0], $this->getCommands()))
        return $this->renderText(sprintf(
          "<li><b>%s%s</b><li><li>This command is not available. You can do : <b>%s</b></li>",
          self::$prompt, $command, implode(' ', $this->getCommands())
        ));
      $exec = sprintf('%s', $command);
    }

    ob_start();

    passthru($exec.' 2>&1', $return);
    $raw = ob_get_clean();
    if ($return > 0)
    {
      return $this->renderText(sprintf(
        "<li><b>%s%s</b><li><li>%s",
        self::$prompt, $command, $raw
      ));
    }
    $arr = explode("\n", $raw);
    $res = sprintf("<li class='dm_command_intro'><b>%s%s</b><li>", self::$prompt, $command);
    foreach($arr as $a)
      $res .= "<li class='dm_result_command'>".$a."</li>";
    return $this->renderText($res);
  }

  public function executeIndex(sfWebRequest $request)
  {
    $this->prompt = self::$prompt;
    $this->commands = implode(' ', $this->getCommands());

    ob_start();
    passthru("uname -a");
    $this->uname = ob_get_clean();
    ob_start();
    passthru("uname -n");
    $this->uname_n = ob_get_clean();
    
    ob_start();
    passthru("whoami");
    $this->whoami = ob_get_clean();
    $this->whoami = trim($this->whoami);
    $this->getUser()->setAttribute('name_shell', $this->whoami.'@'.$this->uname_n);
  }

}
