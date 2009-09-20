<?php

class dmFrontBaseComponents extends dmBaseComponents
{
  /*
   * @return DmPage the current page
   */
  public function getPage()
  {
    return $this->contect->getPage();
  }
}