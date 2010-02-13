<?php

class dmAlternativeHelper extends dmHelper
{
  public function _tagO($tagName, array $opt = array())
  {
    return $this->open($tagName, $opt);
  }

  public function _tagC($tagName)
  {
    return $this->close($tagName);
  }
}