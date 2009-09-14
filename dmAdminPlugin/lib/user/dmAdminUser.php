<?php

class dmAdminUser extends dmUser
{

  /*
   * @return dmTheme the current user theme
   */
  public function getTheme()
  {
    if($this->hasCache('theme'))
    {
      return $this->getCache('theme');
    }
    
    $this->serviceContainer->addParameters(array(
      'theme.options' => array('key' => 'diem_admin', 'path' => 'themeAdmin', 'name' => 'Admin Theme', 'enabled' => true)
    ));
    
    return $this->setCache('theme', $this->serviceContainer->getService('theme'));
  }

  public function getAppliedSearchOnModule($module)
  {
    return $this->getAttribute($module.'.search', '', 'admin_module');
  }

  public function getAppliedFiltersOnModule($module)
  {
    $appliedFilters = array();
    foreach($this->getAttribute($module.'.filters', array(), 'admin_module') as $filter => $value )
    {
      if ($value)
      {
        if (is_array($value))
        {
          if (dmArray::get($value, 'text') || dmArray::get($value, 'is_empty'))
          {
            $appliedFilters[] = $filter;
          }
        }
        else
        {
          $appliedFilters[] = $filter;
        }
      }
    }

    return $appliedFilters;
  }

}