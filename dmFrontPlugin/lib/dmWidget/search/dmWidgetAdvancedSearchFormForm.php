<?php

class dmWidgetAdvancedSearchFormForm extends dmWidgetPluginForm
{

  public function configure()
  {
    parent::configure();
    
    dmDb::table('DmPage')->checkSearchPage();
  }

}