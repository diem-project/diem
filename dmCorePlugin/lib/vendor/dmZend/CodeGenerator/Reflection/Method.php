<?php

class dmZendReflectionMethod extends Zend_Reflection_Method
{
  /**
   * Get method body
   *
   * @return string
   */
  public function getBody()
  {
    $lines = array_slice(
    file($this->getDeclaringClass()->getFileName(), FILE_IGNORE_NEW_LINES),
    $this->getStartLine(),
    ($this->getEndLine() - $this->getStartLine()),
    true
    );

    $firstLine = array_shift($lines);

    if (trim($firstLine) !== '{') {
      array_unshift($lines, $firstLine);
    }

    // HACK : these instructions break code by removing the last }
//    $lastLine = array_pop($lines);
//
//    if (trim($lastLine) !== '}') {
//      array_push($lines, $lastLine);
//    }

    // just in case we had code on the bracket lines
    return rtrim(ltrim(implode("\n", $lines), '{'), '}');
  }
}
