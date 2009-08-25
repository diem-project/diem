<?php

$folders = array();

if ($ancestors = $folder->Node->getAncestors())
{
	foreach($ancestors as $parent)
	{
		$folders[] = £('li', £link(dmMediaTools::getAdminUrlFor($parent))->name($parent->name));
	}
}

$folders[] = £('li', £link(dmMediaTools::getAdminUrlFor($folder))->name($folder->name));

echo £('div#breadCrumb', £('ol', implode("", $folders)));