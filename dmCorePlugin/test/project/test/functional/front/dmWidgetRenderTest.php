<?php

require_once(realpath(dirname(__FILE__).'/../../../..').'/functional/helper/dmFunctionalTestHelper.php');
$helper = new dmFunctionalTestHelper();
$helper->boot('front');

$b = $helper->getBrowser();

$b->info('Render domain/list widget');
$pageId = 1;
$widget = dmDb::table('DmPage')->getTree()->fetchRoot()->PageView->Layout->getArea('left')->Zones[0]->Widgets[0];

$b->get(sprintf('+/dmWidget/render?widget_id=%d&page_id=%d', $widget->id, $pageId))
->checks(array(
  'moduleAction' => 'dmWidget/render'
))
->has('div.dm_test_domain.list');

$b->info('Render navigation/breadcrumb widget');
$widget = dmDb::table('DmPage')->getTree()->fetchRoot()->PageView->Layout->getArea('top')->Zones[0]->Widgets[0];

$b->get(sprintf('+/dmWidget/render?widget_id=%d&page_id=%d', $widget->id, $pageId))
->checks(array(
  'moduleAction' => 'dmWidget/render'
))
->testResponseContent('<ol><li><span class="link dm_current">Home</span></li></ol>');

foreach(dmDb::table('DmPage')->findAll() as $page)
{
  foreach($page->PageView->Areas as $area)
  {
    $zones = $area->Zones->getData();

  //  foreach($page->PageView->Layout->Areas as $area)
  //  {
  //    $zones = array_merge($zones, $area->Zones->getData());
  //  }

    $failed = array();
    $passed = 0;
    foreach($zones as $zone)
    {
      foreach($zone->Widgets as $widget)
      {
        try
        {
          $b->get($url = sprintf('+/dmWidget/render?widget_id=%d&page_id=%d', $widget->id, $page->id));

          if(200 !== $b->getResponse()->getStatusCode())
          {
            $failed[] = $url;
          }
          else
          {
            $passed++;
          }
        }
        catch(dmFormNotFoundException $e)
        {
          
        }
      }
    }

    if(count($failed))
    {
      foreach($failed as $url)
      {
        $b->get($url)->checks();
      }
    }
    elseif($passed)
    {
      $b->test()->pass($passed.' widgets successfully rendered for page '.$page);
    }
  }
}