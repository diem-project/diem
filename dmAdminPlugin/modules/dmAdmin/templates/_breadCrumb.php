<?php

if ($sf_request->getParameter('module') === 'dmAdmin' && $sf_request->getParameter('action') === 'index')
{
	return;
}

echo £o("div#breadCrumb");

echo £o('ol');

foreach(dmAdminHelper::getBreadCrumb() as $part)
{
	echo £('li', $part);
}

include_slot('dm.breadCrumb');

echo £c('ol');

echo £c('div');