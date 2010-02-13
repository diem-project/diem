<?php

class dmWidgetSearchFormForm extends dmWidgetPluginForm
{

  public function configure()
  {
    parent::configure();
    
    dmDb::table('DmPage')->checkSearchPage();
  }

}