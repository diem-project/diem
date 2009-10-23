<?php

class dmSeoValidationService extends dmService
{
  protected
  $attributes;

  protected static
  $uniqueAttributes = array('slug', 'title', 'description');

  public function execute(array $attributes = array('slug'))
  {
    $timer = dmDebug::timerOrNull('dmSeoValidationService::execute');

    $this->attributes = $attributes;

    $this->checkAttributesExist();

    $duplicated = $this->validateAttributes();

    $timer && $timer->addTime();

    return $duplicated;
  }

  protected function validateAttributes()
  {
    $allValues = $duplicated = $this->getAttributesAsArrayKeys();

    /*
     * Let's store all page values
     */
    $allPages = dmDb::query('DmPage p INDEXBY p.id')->fetchRecords();

    foreach($allPages as $page)
    {
      foreach($this->attributes as $attribute)
      {
        $value = $page->get($attribute);

        if (!isset($allValues[$attribute][$value]))
        {
          $allValues[$attribute][$value] = array($page->getId());
        }
        else
        {
          $allValues[$attribute][$value][] = $page->getId();
        }
      }
    }

    /*
     * Now, find values that hove more than one page
     */
    foreach($allValues as $attribute => $values)
    {
      foreach($values as $value => $pages)
      {
        if (count($pages) > 1)
        {
          $duplicated[$attribute][$value] = array();
          foreach($pages as $pageId)
          {
            $duplicated[$attribute][$value][] = $allPages[$pageId];
          }
        }
      }
    }

    foreach($duplicated as $attribute => $values)
    {
      if(!count($values))
      {
        unset($duplicated[$attribute]);
      }
    }

    return $duplicated;
  }

  protected function checkAttributesExist()
  {
    foreach($this->attributes as $attribute)
    {
      if(!in_array($attribute, self::$uniqueAttributes))
      {
        throw new dmException('%s is not in %s', $attribute, implode(', ', self::$uniqueAttributes));
      }
    }
  }

  protected function getAttributesAsArrayKeys()
  {
    $array = array();
    foreach($this->attributes as $attribute)
    {
      $array[$attribute] = array();
    }
    return $array;
  }
}