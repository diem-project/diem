<?php

/*
 * generate action classes for front module
 */
class dmFrontActionGenerator extends dmFrontModuleGenerator
{

	public function execute()
	{
    $file = dmOs::join(sfConfig::get('sf_apps_dir'), 'front', 'modules', $this->module->getKey(), 'actions', 'actions.class.php');

    $this->filesystem->mkdir(dirname($file));

    if (file_exists($file))
    {
    	return true;
    }

    $code = $this->build();

    return file_put_contents($file, $code);
	}

	protected function build()
	{
		return
		$this->getHead().
		$this->getMethods().
		$this->getFoot();
	}

  protected function getHead()
  {
    return "<?php

/*
 * {$this->module->getName()} : actions
 */
class {$this->module->getKey()}Actions extends dmFrontModuleActions
{
";
  }

  protected function getMethods()
  {
  	return '';
  }

  protected function getFoot()
  {
    return "}";
  }

}