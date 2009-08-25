<?php

$env = 'front';
$debug = false;

include(dirname(__FILE__).'/../../bootstrap/functional.php');

$browser = new sfTestFunctional(new sfBrowser());

$memoryLimit = ini_get('memory_limit');

$maxTime = array('?', 0);
$maxMem = array('?', 0);

$totalTime = 0;
$totalMem  = 0;

$nbPages = dmDb::table('DmPage')->count();

/*
 * Request one page to make stats more reliable
 */
$browser->get('/?dm_debug=1');

foreach(dmDb::table('DmPage')->findAll() as $page)
{
	$microtime = microtime(true);
	$memoryUsed = memory_get_usage();

	$browser->
	  get('/'.$page->slug.'?dm_debug=1')->
	  with('response')->begin()->
	    isStatusCode(200)->
	  end()
	;
	 
	$time = sprintf('%01.3f', 1000 * (microtime(true) - $microtime));
	$mem = sprintf('%01.3f', (memory_get_usage()-$memoryUsed)/1024);
	 
	$browser->info(round($time).' ms | '.round($mem).' Ko');

	if ($time > $maxTime[1])
	{
		$maxTime = array($page->slug, $time);
	}
	if ($mem > $maxMem[1])
	{
		$maxMem = array($page->slug, $mem);
	}
	
	$totalTime += $time;
	$totalMem += $mem;
}

$averageTime = $totalTime / $nbPages;
$averageMem = $totalMem / $nbPages;

$browser->info('------------------------------------------------');

$browser->info(sprintf('Average time : %01.3f ms', $averageTime));
$browser->info(sprintf('Average memory : %01.3f Ko', $averageMem));

$browser->info(sprintf('Max time : %01.3f ms on %s', $maxTime[1], $maxTime[0]));
$browser->info(sprintf('Max memory : %01.3f Ko on %s', $maxMem[1], $maxMem[0]));