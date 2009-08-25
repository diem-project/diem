<?php

class dmRefreshService extends dmService
{

  public function execute()
  {
    $this->executeService('dmClearCache');

    $this->executeService('dmPageSync');

    $this->executeService('dmUpdateSeo');
  }

}