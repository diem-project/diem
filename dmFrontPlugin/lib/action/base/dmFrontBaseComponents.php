<?php

class dmFrontBaseComponents extends dmBaseComponents
{

  public function getPage()
  {
    return $this->getDmContext()->getPage();
  }
  
  public function getSite()
  {
    return $this->getDmContext()->getSite();
  }
}