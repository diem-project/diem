<?php

/*
 * @return $return if $source is the current page
 */
function dm_current($source = null, $return = '.dm_current')
{
  return (($page = dmContext::getInstance()->getPage()) && $page->isSource($source)) ? $return : null;
}

/*
 * @return $return if $source is parent of the current page
 */
function dm_parent($source = null, $return = '.dm_parent')
{
  return (($page = dmContext::getInstance()->getPage()) && $page->isDescendantOfSource($source)) ? $return : null;
}

/*
 * @return $return if $source is equal or parent of the current page
 */
function dm_current_or_parent($source = null, $return = '.dm_current_or_parent')
{
  return (($page = dmContext::getInstance()->getPage()) && ($page->isSource($source) || $page->isDescendantOfSource($source))) ? $return : null;
}