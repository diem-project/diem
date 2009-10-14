<?php

class dmFrontActionTemplateGenerator extends dmFrontModuleGenerator
{

  public function execute()
  {
    $dir = dmOs::join(sfConfig::get('sf_apps_dir'), 'front', 'modules', $this->module->getKey(), 'templates');

    $this->filesystem->mkdir($dir);

    $success = true;

    foreach($this->module->getActions() as $action)
    {
      $file = dmOs::join($dir, '_'.$action->getKey().'.php');

      if(file_exists($file))
      {
        continue;
      }

      touch($file);

      $code = $this->getActionTemplate($action);

      $success &= (bool) file_put_contents($file, $code);

      $success &= (bool) chmod($file, 0777);
    }

    return $success;
  }

  protected function getActionTemplate(dmAction $action)
  {
    switch($action->getType())
    {
      case 'list': return $this->getListActionTemplate($action); break;
      case 'show': return $this->getShowActionTemplate($action); break;
      case 'form': return $this->getFormActionTemplate($action); break;
      default:     return $this->getUserActionTemplate($action); break;
    }
  }

  protected function getListActionTemplate(dmAction $action)
  {
    $object = '$'.$this->module->getKey();
    $pager = $object.'Pager';
    $vars = $this->getVarsComment(array($pager));
    return "<?php
/*
 * Action for {$this->module->getName()} : {$action->getName()}
 * Vars : {$vars}
 */

echo £o('div.{$this->module->getUnderscore()}.{$action->getUnderscore()}');

 echo {$pager}->renderNavigationTop();

  echo £o('ul.elements');

  foreach ({$pager} as \${$this->module->getKey()})
  {
    echo £o('li.element');
    
      echo ".($this->module->hasPage() ? "£link(\${$this->module->getKey()});" : "\${$this->module->getKey()};")."
      
    echo £c('li');
  }

  echo £c('ul');

 echo {$pager}->renderNavigationBottom();

echo £c('div');";
  }

  protected function getShowActionTemplate(dmAction $action)
  {
    $object = '$'.$this->module->getKey();
    $vars = $this->getVarsComment(array($object));
    return "<?php
/*
 * Action for {$this->module->getName()} : {$action->getName()}
 * Vars : {$vars}
 */

echo £o('div.{$this->module->getUnderscore()}.{$action->getUnderscore()}');

  echo \${$this->module->getKey()};
  
echo £c('div');
";
  }

  protected function getFormActionTemplate(dmAction $action)
  {
    $vars = $this->getVarsComment(array('form'));
    return "<?php
/*
 * Action for {$this->module->getName()} : {$action->getName()}
 * Vars : {$vars}
 */

echo \$form;
";
  }

  protected function getUserActionTemplate(dmAction $action)
  {
    return "<?php
";
  }

  protected function getVarsComment($vars)
  {
    foreach($vars as $key => $name)
    {
      if ($name{0} !== '$')
      {
        $vars[$key] = '$'.$name;
      }
    }

    return implode(', ', $vars);
  }

}