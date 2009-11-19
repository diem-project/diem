<?php

require_once dirname(__FILE__).'/../lib/dmLayoutGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/dmLayoutGeneratorHelper.class.php';

/**
 * dmLayout actions.
 *
 * @package    diem
 * @subpackage dmLayout
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class dmLayoutActions extends autoDmLayoutActions
{
  
  public function executeDuplicate(dmWebRequest $request)
  {
    $this->forward404Unless(
      $layout = dmDb::query('DmLayout l')
      ->where('l.id = ?', $request->getParameter('id'))
      ->leftJoin('l.Areas a')
      ->leftJoin('a.Zones z')
      ->leftJoin('z.Widgets w')
      ->fetchOne()
    );
    
    $newLayout = dmDb::create('DmLayout', array(
      'css_class' => $layout->cssClass,
      'name' => $layout->name
    ));
    
    do
    {
      $newLayout->set('name', $newLayout->get('name').' copy');
    }
    while(dmDb::query('DmLayout l')->where('l.name = ?', $newLayout->get('name'))->exists());
    
    foreach($layout->get('Areas') as $area)
    {
      $newArea = $area->copy(false);
      
      foreach($area->get('Zones') as $zone)
      {
        $newZone = $zone->copy(false);
        
        foreach($zone->get('Widgets') as $widget)
        {
          $newZone->Widgets[] = $widget->copy(false);
        }
        
        $newArea->Zones[] = $newZone;
      }
      
      $newLayout->Areas[] = $newArea;
    }

    $newLayout->save();
    
    return $this->redirect($this->context->getHelper()->£link($newLayout)->getHref());
  }
}