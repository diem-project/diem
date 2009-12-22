<?php

class dmFrontActionGenerator extends dmFrontModuleGenerator
{
  protected
  $class,
  $indentation = '  ';

  public function execute()
  {
    require_once(dmOs::join(sfConfig::get('dm_core_dir'), 'lib/vendor/Zend/Reflection/File.php'));
    require_once(dmOs::join(sfConfig::get('dm_core_dir'), 'lib/vendor/Zend/CodeGenerator/Php/File.php'));
    require_once(dmOs::join(sfConfig::get('dm_core_dir'), 'lib/vendor/dmZend/CodeGenerator/Php/Class.php'));
    
    $file = dmOs::join($this->moduleDir, 'actions/actions.class.php');

    if (!$this->filesystem->mkdir(dirname($file)))
    {
      $this->logError('can not create directory '.dmProject::unrootify(dirname($file)));
    }
    
    $className = $this->module->getKey().'Actions';
    
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
    
    if ($this->module->hasModel())
    {
      foreach($this->module->getActions() as $action)
      {
        if ($action->getType() == 'form')
        {
          $methodName = 'execute'.dmString::camelize($action->getKey()).'Widget';
          
          if (!$this->class->getMethod($methodName))
          {
            $this->class->setMethod($this->buildFormMethod($methodName, $action));
          }
        }
      }
    }

    if ($code = $this->class->generate())
    {
      $return = file_put_contents($file, "<?php\n".$code) && $this->filesystem->chmod($file, 0777);
    }
    else
    {
      $return = true;
    }
    
    if(!$return)
    {
      $this->logError('can not write to '.dmProject::unrootify($file));
    }
    
    return $return;
  }
  
  protected function buildClass($className)
  {
    return new Zend_CodeGenerator_Php_Class(array(
      'name' => $className,
      'extendedClass' => 'myFrontModuleActions',
      'docBlock' => array(
        'shortDescription' => $this->module->getName().' actions'
      )
    ));
  }
  
  protected function buildFormMethod($methodName, dmAction $action)
  {
    $body = "\$form = \$this->forms['{$this->module->getModel()}'];
    
if (\$request->isMethod('post') && \$form->bindAndValid(\$request))
{
  \$form->save();
  \$this->redirectBack();
}";
    
    return new dmZendCodeGeneratorPhpMethod(array(
      'indentation' => $this->indentation,
      'name'        => $methodName,
      'visibility'  => 'public',
      'body'        => $body,
      'parameters'  => array(
        array('name' => 'request', 'type' => 'dmWebRequest')
      )
//      'docblock'    => new Zend_CodeGenerator_Php_Docblock(array(
//        'shortDescription' => $action->getName()
//      ))
    ));
  }
}