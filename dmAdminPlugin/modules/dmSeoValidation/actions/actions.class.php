<?php

class dmSeoValidationActions extends dmAdminBaseActions
{

  public function executeIndex(sfWebRequest $request)
  {
    $this->duplicated = $this->getDmContext()->getCacheManager()->getCache('dm/seo/validation')->get('duplicated');
  }

}