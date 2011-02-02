<?php

class dmAdminSortTableForm extends dmAdminSortForm
{
  protected
  $records;
  
  public function configure()
  {
    $this->configureRecordFields();
  }
}