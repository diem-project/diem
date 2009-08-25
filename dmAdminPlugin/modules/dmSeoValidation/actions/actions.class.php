<?php

class dmSeoValidationActions extends dmAdminBaseActions
{

  public function executeIndex(sfWebRequest $request)
  {
    $this->duplicated = dmCacheManager::getCache('dm/seo/validation')->get('duplicated');
  }

}