<?php

class dmFrontActionTemplateGenerator extends dmFrontModuleGenerator
{

  public function execute()
  {
    $dir = dmOs::join($this->moduleDir, 'templates');

    if (!$this->filesystem->mkdir($dir))
    {
      $this->logError('can not create directory '.$dir);
    }

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

      $fileSuccess = (file_put_contents($file, $code) && $this->filesystem->chmod($file, 0777));    
      
      if(!$fileSuccess)
      {
        $this->logError('can not write to '.dmProject::unrootify($file));
      }
      
      $success &= $fileSuccess;
    }

    return $success;
  }

  protected function getActionTemplate(dmModuleComponent $action)
  {
    switch($action->getType())
    {
      case 'list': return $this->getListActionTemplate($action); break;
      case 'show': return $this->getShowActionTemplate($action); break;
      case 'form': return $this->getFormActionTemplate($action); break;
      default:     return $this->getUserActionTemplate($action); break;
    }
  }

  protected function getListActionTemplate(dmModuleComponent $action)
  {
    $object = '$'.$this->module->getKey();
    $pager = $object.'Pager';
    $vars = $this->getVarsComment(array($pager));
    return "<?php // Vars: {$vars}

echo {$pager}->renderNavigationTop();

echo £o('ul.elements');

foreach ({$pager} as \${$this->module->getKey()})
{
  echo £o('li.element');

    echo ".($this->module->hasPage() ? "£link(\${$this->module->getKey()});" : "\${$this->module->getKey()};")."

  echo £c('li');
}

echo £c('ul');

echo {$pager}->renderNavigationBottom();";
  }

  protected function getShowActionTemplate(dmModuleComponent $action)
  {
    $object = '$'.$this->module->getKey();
    $vars = $this->getVarsComment(array($object));
    return "<?php // Vars: {$vars}

echo \${$this->module->getKey()};";
  }

  protected function getFormActionTemplate(dmModuleComponent $action)
  {
    $vars = $this->getVarsComment(array('form'));
    return "<?php // Vars: {$vars}

echo \$form;";
  }

  protected function getUserActionTemplate(dmModuleComponent $action)
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