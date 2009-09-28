<?php

if ($ancestors = $folder->getNode()->getAncestors())
{
  foreach($ancestors as $parent)
  {
    echo £('li', £link(dmMediaTools::getAdminUrlFor($parent))->text($parent->get('name')));
  }
}

echo £('li', £link(dmMediaTools::getAdminUrlFor($folder))->text($folder->get('name')));