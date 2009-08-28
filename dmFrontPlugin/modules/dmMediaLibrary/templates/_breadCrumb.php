<?php

if ($folder->isRoot())
{
	return;
}

slot('dm.breadCrumb');

$parents = array();

foreach($folder->getPath() as $parent)
{
	if (!$parent->isRoot())
	{
	  $parents[] = £('li', £link(dmMediaTools::getAdminUrlFor($parent))->text($parent->getName()));
	}
}

$bread = implode("", $parents);

echo $bread;

end_slot();