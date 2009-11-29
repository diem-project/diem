<?php

class dmZendCodeGeneratorPhpMethod extends Zend_CodeGenerator_Php_Method
{

  public static function fromReflection(Zend_Reflection_Method $reflectionMethod)
  {
    $method = new self();

    $method->setSourceContent($reflectionMethod->getContents(false));
    $method->setSourceDirty(false);

    if ($reflectionMethod->getDocComment() != '') {
      $method->setDocblock(Zend_CodeGenerator_Php_Docblock::fromReflection($reflectionMethod->getDocblock()));
    }

    $method->setFinal($reflectionMethod->isFinal());

    if ($reflectionMethod->isPrivate()) {
      $method->setVisibility(self::VISIBILITY_PRIVATE);
    } elseif ($reflectionMethod->isProtected()) {
      $method->setVisibility(self::VISIBILITY_PROTECTED);
    } else {
      $method->setVisibility(self::VISIBILITY_PUBLIC);
    }

    $method->setStatic($reflectionMethod->isStatic());

    $method->setName($reflectionMethod->getName());

    foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
      $method->setParameter(Zend_CodeGenerator_Php_Parameter::fromReflection($reflectionParameter));
    }

    $body = $reflectionMethod->getBody();
    $body2 = str_replace("\n\n", "\n", $body);
    $body2 = preg_replace("|^\n\s{4}|muU", "\n", $body2);
    $body2 = preg_replace("|^\s{4}|muU", "", $body2);
//    $body2 = str_replace(' ', '.', $body2);
//dmDebug::kill($body, "\n".$body2);
    $method->setBody($body2);

    return $method;
  }

  /**
   * generate()
   *
   * @return string
   */
  public function generate()
  {
    $output = '';

    $indent = $this->getIndentation();

    if (($docblock = $this->getDocblock()) !== null) {
      $docblock->setIndentation($indent);
      $output .= $docblock->generate();
    }

    $output .= $indent;

    if ($this->isAbstract()) {
      $output .= 'abstract ';
    } else {
      $output .= (($this->isFinal()) ? 'final ' : '');
    }

    $output .= $this->getVisibility()
    . (($this->isStatic()) ? ' static' : '')
    . ' function ' . $this->getName() . '(';

    $parameters = $this->getParameters();
    if (!empty($parameters)) {
      foreach ($parameters as $parameter) {
        $parameterOuput[] = $parameter->generate();
      }

      $output .= implode(', ', $parameterOuput);
    }

    $output .= ')' . self::LINE_FEED . $indent . '{' . self::LINE_FEED;

    if ($this->_body) {
      $output .= str_repeat($indent, 2)
      .  str_replace(self::LINE_FEED, self::LINE_FEED . $indent . $indent, trim($this->_body))
      .  self::LINE_FEED;
    }

    $output .= $indent . '}' . self::LINE_FEED;
    
    return $output;
  }

}