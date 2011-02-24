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
    
    $file = dmOs::join($this->moduleDir, 'actions/components.class.php');

    if (!$this->filesystem->mkdir(dirname($file)))
    {
      $this->logError('can not create directory '.dmProject::unrootify(dirname($file)));
    }
    
    $className = $this->module->getKey().'Components';
    
    if (file_exists($file))
    {
      if($this->shouldSkip($file))
      {
        $this->log('skip '.dmProject::unrootify($file));
        return true;
      }
      
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
    
    foreach($this->module->getComponents() as $component)
    {
      $methodName = 'execute'.dmString::camelize($component->getKey());
      
      if (!$this->class->getMethod($methodName))
      {
        $this->class->setMethod($this->buildActionMethod($methodName, $component));
      }
    }

    if ($code = $this->class->generate())
    {
      $return = file_put_contents($file, "<?php\n".$code);
      $this->filesystem->chmod($file, 0777);
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

  protected function shouldSkip($file)
  {
    $code = file_get_contents($file);

    return false !== strpos($code, 'require_once');
  }
  
  protected function buildClass($className)
  {
    return new Zend_CodeGenerator_Php_Class(array(
      'name' => $className,
      'extendedClass' => 'myFrontModuleComponents',
      'docBlock' => array(
        'shortDescription' => $this->module->getName().' components',
        'longDescription' => 'No redirection nor database manipulation ( insert, update, delete ) here'
      )
    ));
  }
  
  protected function buildActionMethod($methodName, dmModuleComponent $component)
  {
    switch($component->getType())
    {
      case 'list': 
        $body = "\$query = \$this->getListQuery();

\$this->{$this->module->getKey()}Pager = \$this->getPager(\$query);";
        break;
      case 'show':
        $body = "\$query = \$this->getShowQuery();

\$this->{$this->module->getKey()} = \$this->getRecord(\$query);";
        break;
      case 'form':
        $body = "\$this->form = \$this->forms['{$this->module->getModel()}'];";
        break;
      default:
        $body = "// Your code here";
    }
    
    return new dmZendCodeGeneratorPhpMethod(array(
      'indentation' => $this->indentation,
      'name'        => $methodName,
      'visibility'  => 'public',
      'parameters'  => array(
        array('name' => 'request', 'type' => 'dmWebRequest')
      ),
      'body'        => $body
    ));
  }
}