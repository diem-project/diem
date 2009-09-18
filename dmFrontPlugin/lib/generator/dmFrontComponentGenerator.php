<?php

class dmFrontComponentGenerator extends dmFrontModuleGenerator
{
  protected
  $class,
  $indentation = '  ';

  public function execute()
  {
    require_once(dmOs::join(sfConfig::get('dm_core_dir'), 'lib/vendor/Zend/Reflection/File.php'));
    require_once(dmOs::join(sfConfig::get('dm_core_dir'), 'lib/vendor/Zend/CodeGenerator/Php/File.php'));
    require_once(dmOs::join(sfConfig::get('dm_core_dir'), 'lib/vendor/dmZend/CodeGenerator/Php/Class.php'));
    
    $file = dmOs::join(sfConfig::get('sf_apps_dir'), 'front/modules', $this->module->getKey(), 'actions/components.class.php');

    $this->filesystem->mkdir(dirname($file));
    
    $className = $this->module->getKey().'Components';
    
    if (file_exists($file))
    {
      include_once($file);
      $this->class = dmZendCodeGeneratorPhpClass::fromReflection(new Zend_Reflection_Class($className));
      
      foreach($this->class->getMethods() as $method)
      {
        $method->setIndentation($this->indentation);
      }
    }
    else
    {
      $this->class = $this->buildClass($className);
    }
    
    $this->class->setIndentation($this->indentation);
    
    foreach($this->module->getActions() as $action)
    {
      $methodName = 'execute'.dmString::camelize($action->getKey());
      
      if (!$this->class->getMethod($methodName))
      {
        $this->class->setMethod($this->buildActionMethod($methodName, $action));
      }
    }

    if ($code = $this->class->generate())
    {
      $return = file_put_contents($file, "<?php\n".$code);
      @chmod($file, 0777);
    }
    else
    {
      $return = true;
    }

    return $return;
  }
  
  protected function buildClass($className)
  {
    return new Zend_CodeGenerator_Php_Class(array(
      'name' => $className,
      'extendedClass' => 'dmFrontModuleComponents',
      'docBlock' => array(
        'shortDescription' => $this->module->getName().' components',
        'longDescription' => 'Components are micro-controllers that prepare data for a template.
You should not use redirection or database manipulation ( insert, update, delete ) here.
To make redirections or manipulate database, use the actions class.'
      )
    ));
  }
  
  protected function buildActionMethod($methodName, dmAction $action)
  {
    switch($action->getType())
    {
      case 'list': 
        $body = "\$query = \$this->getListQuery();
\$this->{$this->module->getKey()}Pager = \$this->getPager(\$query);";
        break;
      case 'show':
        $body = "\$query = \$this->getShowQuery();
\$this->{$this->module->getKey()} = \$this->getRecord(\$query);";
        break;
      default:
        $body = "// Your code here";
    }
    
    return new dmZendCodeGeneratorPhpMethod(array(
      'indentation' => $this->indentation,
      'name'        => $methodName,
      'visibility'  => 'public',
      'body'        => $body
//      'docblock'    => new Zend_CodeGenerator_Php_Docblock(array(
//        'shortDescription' => $action->getName()
//      ))
    ));
  }
  
//  protected function buildPagerQueryMethod($methodName)
//  {
//    return new dmZendCodeGeneratorPhpMethod(array(
//      'indentation' => $this->indentation,
//      'name'        => $methodName,
//      'visibility'  => 'protected',
//      'body'        => "return \$this->getDmModule()->getTable()->createQuery('{$this->module->getKey()}')->whereIsActive(true, '{$this->module->getModel()}');",
//      'parameters'  => array(),
//      'docblock'    => new Zend_CodeGenerator_Php_Docblock(array(
//        'shortDescription' => 'Create the default pager query for this module list widgets',
//        'longDescription'  => "If you want to add custom joins or other stuff to the list queries,\nthis is the right place.",
//        'tags'             => array(
//          new Zend_CodeGenerator_Php_Docblock_Tag_Return(array(
//            'paramName' => 'query',
//            'datatype'  => 'myDoctrineQuery'
//          )),
//        ),
//      ))
//    ));
//  }
}