<?php

require_once 'dmZend/CodeGenerator/Php/Method.php';

class dmZendCodeGeneratorPhpClass extends Zend_CodeGenerator_Php_Class
{
  /**
   * fromReflection() - build a Code Generation PHP Object from a Class Reflection
   *
   * @param Zend_Reflection_Class $reflectionClass
   * @return dmZendCodeGeneratorPhpClass
   */
  public static function fromReflection(Zend_Reflection_Class $reflectionClass)
  {
    $class = new self();

    $class->setSourceContent($class->getSourceContent());
    $class->setSourceDirty(false);

    if ($reflectionClass->getDocComment() != '') {
      $class->setDocblock(Zend_CodeGenerator_Php_Docblock::fromReflection($reflectionClass->getDocblock()));
    }

    $class->setAbstract($reflectionClass->isAbstract());
    $class->setName($reflectionClass->getName());

    if ($parentClass = $reflectionClass->getParentClass()) {
      $class->setExtendedClass($parentClass->getName());
      $interfaces = array_diff($parentClass->getInterfaces(), $reflectionClass->getInterfaces());
    } else {
      $interfaces = $reflectionClass->getInterfaces();
    }

    $class->setImplementedInterfaces($interfaces);

    $properties = array();
    foreach ($reflectionClass->getProperties() as $reflectionProperty) {
      if ($reflectionProperty->getDeclaringClass()->getName() == $class->getName()) {
        $properties[] = Zend_CodeGenerator_Php_Property::fromReflection($reflectionProperty);
      }
    }
    $class->setProperties($properties);

    $methods = array();
    foreach ($reflectionClass->getMethods(-1, 'dmZendReflectionMethod') as $reflectionMethod) {
      if ($reflectionMethod->getDeclaringClass()->getName() == $class->getName()) {
        $methods[] = dmZendCodeGeneratorPhpMethod::fromReflection($reflectionMethod);
      }
    }
    $class->setMethods($methods);

    return $class;
  }
}