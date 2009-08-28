<?php

if (!$pager->haveToPaginate())
{
  return;
}

echo £o("div.search_pager");

if (!$pager->atFirstPage())
{
  echo £link('dmSearchEngine/search'.$pager->getPageUrl($pager->getPreviousPage()))
    ->text("Précédent")
    ->format(false);
}
foreach ($pager->getLinks(5) as $link)
{
  if ($link == $pager->getPage())
  {
    echo £("strong", $link);
  }
  else
  {
    echo £link('dmsSearchAdmin/search'.$pager->getPageUrl($link))
      ->text($link)
      ->format(false);
  }
}

if (!$pager->atLastPage())
{
  echo £link('dmsSearchAdmin/search'.$pager->getPageUrl($pager->getNextPage()))
    ->text("Suivant")
    ->format(false);
}

echo £c("div");