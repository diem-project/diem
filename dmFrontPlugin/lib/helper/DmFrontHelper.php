<?php

/*
 * @return $return if $source is the current page
 */
function dm_current($source = null, $return = '.dm_current')
{
  if(($page = dmDb::table('DmPage')->findOneBySource($source)) && ($currentPage = dmContext::getInstance()->getPage()))
  {
    if ($currentPage->get('id') === $page->get('id'))
    {
      return $return;
    }
  }

  return null;
}

/*
 * @return $return if $source is parent of the current page
 */
function dm_parent($source = null, $return = '.dm_parent')
{
  if(($page = dmDb::table('DmPage')->findOneBySource($source)) && ($currentPage = dmContext::getInstance()->getPage()))
  {
    if($currentPage->getNode()->isDescendantOf($page))
    {
      return $return;
    }
  }

  return null;
}

/*
 * @return $return if $source is equal or parent of the current page
 */
function dm_current_or_parent($source = null, $return = '.dm_current_or_parent')
{
  if(($page = dmDb::table('DmPage')->findOneBySource($source)) && ($currentPage = dmContext::getInstance()->getPage()))
  {
    if($currentPage->get('id') === $page->get('id') || $currentPage->getNode()->isDescendantOf($page))
    {
      return $return;
    }
  }

  return null;
}