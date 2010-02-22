<?php

abstract class dmAdminBaseServiceContainer extends dmBaseServiceContainer
{
  
  protected function connectServices()
  {
    parent::connectServices();

    $this->getService('bread_crumb')->connect();
  }
}