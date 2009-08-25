<?php

class dmBaseComponents extends sfComponents
{
  protected function getDmContext()
  {
    return dmContext::getInstance();
  }
}