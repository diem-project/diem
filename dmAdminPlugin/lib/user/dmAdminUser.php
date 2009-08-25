<?php

class dmAdminUser extends dmUser
{

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

  public function getTheme()
  {
  	if($this->hasCache('theme'))
  	{
  		return $this->getCache('theme');
  	}

  	return $this->setCache('theme', dmTheme::getTheme('themeAdmin'));
  }

}