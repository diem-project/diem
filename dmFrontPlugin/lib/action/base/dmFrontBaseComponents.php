<?php

class dmFrontBaseComponents extends dmBaseComponents
{

  public function getPage()
  {
    return $this->getDmContext()->getPage();
  }
  
}