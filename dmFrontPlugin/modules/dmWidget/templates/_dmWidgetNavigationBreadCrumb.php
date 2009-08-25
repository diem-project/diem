<?php

$nbPages = count($pages);

echo £o('ol');

foreach($pages as $position => $page)
{
	echo £('li',
	  £link($page)
	);

	if ($position < ($nbPages-1))
	{
		echo £('li', $separator);
	}
}

echo £c('ol');