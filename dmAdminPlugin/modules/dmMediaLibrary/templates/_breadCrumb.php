<?php

$folders = array();

if ($ancestors = $folder->Node->getAncestors())
{
  foreach($ancestors as $parent)
  {
    $folders[] = £('li', £link(dmMediaTools::getAdminUrlFor($parent))->text($parent->name));
  }
}

$folders[] = £('li', £link(dmMediaTools::getAdminUrlFor($folder))->text($folder->name));

echo £('div#breadCrumb', £('ol', implode("", $folders)));