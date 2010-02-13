<?php

if (!$pager->haveToPaginate())
{
  return;
}

echo _open("div.search_pager");

if (!$pager->atFirstPage())
{
  echo _link('dmSearchEngine/search'.$pager->getPageUrl($pager->getPreviousPage()))
    ->text("Précédent")
    ->format(false);
}
foreach ($pager->getLinks(5) as $link)
{
  if ($link == $pager->getPage())
  {
    echo _tag("strong", $link);
  }
  else
  {
    echo _link('dmsSearchAdmin/search'.$pager->getPageUrl($link))
      ->text($link)
      ->format(false);
  }
}

if (!$pager->atLastPage())
{
  echo _link('dmsSearchAdmin/search'.$pager->getPageUrl($pager->getNextPage()))
    ->text("Suivant")
    ->format(false);
}

echo _close("div");