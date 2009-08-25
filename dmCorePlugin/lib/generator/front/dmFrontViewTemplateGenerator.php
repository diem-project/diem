<?php

class dmFrontViewTemplateGenerator extends dmFrontModuleGenerator
{

	public function execute()
	{
    $dir = dmOs::join(sfConfig::get('sf_apps_dir'), 'front', 'modules', $this->module->getKey(), 'templates/views');

    $this->filesystem->mkdir($dir);

    $success = true;

    foreach($this->module->getViews() as $view)
    {
    	$file = dmOs::join($dir, $view->getKey().'.php');

    	if(file_exists($file))
    	{
    		continue;
    	}

    	touch($file);

    	$code = $this->getViewTemplate($view);

      $success &= (bool) file_put_contents($file, $code);
    }

    return $success;
	}

	protected function getViewTemplate(dmView $view)
	{
    $object = '$'.$this->module->getKey();

    $displayInstruction = $this->module->hasPage()
    ? "£link($object)"
    : $object;

    return "<?php
/*
 * View for {$this->module->getName()} : {$view->getName()}
 * Vars : {$object}
 */

echo {$displayInstruction};
";
	}
}