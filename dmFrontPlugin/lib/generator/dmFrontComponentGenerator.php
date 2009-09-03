<?php

class dmFrontComponentGenerator extends dmFrontModuleGenerator
{

	public function execute()
	{
    require_once(dmOs::join(sfConfig::get('dm_core_dir'), 'lib/vendor/Zend/Reflection/File.php'));
    require_once(dmOs::join(sfConfig::get('dm_core_dir'), 'lib/vendor/Zend/CodeGenerator/Php/File.php'));
    require_once(dmOs::join(sfConfig::get('dm_core_dir'), 'lib/vendor/dmZend/CodeGenerator/Php/Class.php'));
    
    $file = dmOs::join(sfConfig::get('sf_apps_dir'), 'front/modules', $this->module->getKey(), 'actions/components.class.php');

    $this->filesystem->mkdir(dirname($file));

    if (file_exists($file))
    {
    	include_once($file);
      $class = dmZendCodeGeneratorPhpClass::fromReflection(new Zend_Reflection_Class($this->getClassName()));
    }
    else
    {
    	$class = new Zend_CodeGenerator_Php_Class;
      return;
    }
    
    $class->setIndentation('  ');

    $code = $this->build();
    
    $class->setMethod(new dmZendCodeGeneratorPhpMethod(array(
    'name'       => 'setBaz',
    'parameters' => array(
        array('name' => 'baz'),
    ),
    'body'       => '$this->_baz = $baz;' . "\n" . 'return $this;',
    'docblock'   => new Zend_CodeGenerator_Php_Docblock(array(
        'shortDescription' => 'Set the baz property',
        'tags'             => array(
            new Zend_CodeGenerator_Php_Docblock_Tag_Param(array(
                'paramName' => 'baz',
                'datatype'  => 'string'
            )),
            new Zend_CodeGenerator_Php_Docblock_Tag_Return(array(
                'datatype'  => 'string',
            )),
        ),
    )),
)));

    foreach($class->getMethods() as $method)
    {
      $method->setIndentation('  ');
    }

    $code = $this->build();
    
    dmDebug::kill($file, $code, $class->generate());

    return file_put_contents($file, $code);
	}
	
	protected function getClassName()
	{
//	  return 'dmFrontModuleComponents';
		return $this->module->getKey().'Components';
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
 * {$this->module->getName()} : components
 */
class {$this->module->getKey()}Components extends dmFrontModuleComponents
{
";
  }

  protected function getMethods()
  {
  	$code = '';
  	foreach($this->module->getActions() as $action)
  	{
  		switch($action->getType())
  		{
  			case 'list': $code .= $this->getListMethod($action); break;
  			case 'show': $code .= $this->getShowMethod($action); break;
  			default:
  		    $code .= $this->getUserMethod($action);
  		}
  	}
  	
  	if ($this->module->hasModel())
  	{
  	  $code .= $this->getListQueryMethod();
  	}
  	
  	return $code;
  }

  protected function getListQueryMethod()
  {
  	$moduleKey = $this->module->getKey();

    return "
  /*
   * This query is used by {$this->module->getName()} list components
   */
  protected function getPagerQuery()
  {
    return \$this->getDmModule()->getTable()->createQuery('".$moduleKey{0}."')
      ->whereIsActive(true, '{$this->module->getModel()}')
    ;
  }
";
  }
  
  protected function getListMethod(dmAction $action)
  {
    return "
  /*
   * {$action->getName()}
   */
  public function execute".dmString::camelize($action->getKey())."()
  {
    \$query = \$this->getPagerQuery();
    
    \$this->{$this->module->getKey()}Pager = \$this->getListPager(\$query);
  }
";
  }

  protected function getShowMethod(dmAction $action)
  {
    return "
  /*
   * {$action->getName()}
   */
  public function executeShow()
  {
    \$this->{$this->module->getKey()} = \$this->getShowRecord();
  }
";
  }

  protected function getFormMethod(dmAction $action)
  {
    return "
  /*
   * {$action->getName()}
   */
  public function executeForm()
  {
  }
";
  }

  protected function getUserMethod(dmAction $action)
  {
    return "
  /*
   * {$action->getName()}
   */
//  public function execute".dmString::camelize($action->getKey())."()
//  {
//  }
";
  }

  protected function getFoot()
  {
    return "}";
  }

}