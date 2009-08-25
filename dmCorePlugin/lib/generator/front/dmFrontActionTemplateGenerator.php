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
		$vars = $this->getVarsComment(array($pager, 'view'));
    return "<?php
/*
 * Action for {$this->module->getName()} : {$action->getName()}
 * Vars : {$vars}
 */

echo £o('div.{$this->module->getUnderscore()}.{$action->getUnderscore()}.'.\$view);

 echo {$pager}->getNavigationTop();

  echo £o('ul.elements');

  foreach ({$pager}->getResults() as \${$this->module->getKey()})
  {
    echo £('li.element', \$view->render(\${$this->module->getKey()}));
  }

  echo £c('ul');

 echo {$pager}->getNavigationBottom();

echo £c('div');";
	}

  protected function getShowActionTemplate(dmAction $action)
  {
    $object = '$'.$this->module->getKey();
    $vars = $this->getVarsComment(array($object, 'view'));
    return "<?php
/*
 * Action for {$this->module->getName()} : {$action->getName()}
 * Vars : {$vars}
 */


echo £('div.{$this->module->getUnderscore()}.{$action->getUnderscore()}.'.\$view,

  \$view->render(\${$this->module->getKey()})
  
);
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