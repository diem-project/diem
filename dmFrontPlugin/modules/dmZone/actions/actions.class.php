<?php

class dmZoneActions extends dmFrontBaseActions
{

  public function executeEdit(sfWebRequest $request)
  {
    $this->forward404Unless(
      $this->zone = dmDb::table('DmZone')->find($request->getParameter('zone_id'))
    );

    $this->getServiceContainer()->setParameter('zone_form.object', $this->zone);
    $this->form = $this->context->get('zone_form');
    
    $this->form->removeCsrfProtection();

    if ($request->isMethod('post') && $this->form->bindAndValid($request))
    {
      $this->form->updateObject();
      
      if ($request->hasParameter('and_save'))
      {
        $this->form->getObject()->save();
        return $this->renderText('ok');
      }
    }
  }

  public function executeGetAttributes(sfWebRequest $request)
  {
    $this->forward404Unless(
      $zone = dmDb::query('DmZone z')
      ->where('z.id = ?', $request->getParameter('zone_id'))
      ->select('z.width as width, z.css_class as css_class')
      ->limit(1)
      ->fetchPDO()
    );
    
    return $this->renderJson($zone[0]);
  }

  public function executeDelete(sfWebRequest $request)
  {
    $this->forward404Unless(
      $zone = dmDb::table('DmZone')->find($request->getParameter('zone_id'))
    );

    $zone->delete();

    return $this->renderText('ok');
  }

  public function executeSort(sfWebRequest $request)
  {
    $this->forward404Unless(
      $area = dmDb::table('DmArea')->find($request->getParameter('dm_area')),
      'Can not find area'
    );

    $this->forward404Unless(
      $zoneList = $request->getParameter('dm_zone'),
      'Missing zone list'
    );

    $this->sortZones($zoneList);

    return $this->renderText('ok');
  }

  public function executeAdd(sfWebRequest $request)
  {
    $this->forward404Unless(
      $toArea = dmDb::table('DmArea')->find($request->getParameter('to_dm_area')),
      'Can not find to area'
    );

    $zone = dmDb::create('DmZone')->fromArray(array('dm_area_id' => $toArea->id))->saveGet();

    $helper = $this->context->get('page_helper');

    return $this->renderText($helper->renderZone($zone->toArray(), true));
  }

  public function executeMove(sfWebRequest $request)
  {
    $this->forward404Unless(
      $zone = dmDb::table('DmZone')->find($request->getParameter('moved_dm_zone')),
      'Can not find zone'
    );

    $this->forward404Unless(
      $toArea = dmDb::table('DmArea')->find($request->getParameter('to_dm_area')),
      'Can not find to area'
    );

    $this->forward404Unless(
      $zoneList = $request->getParameter('dm_zone'),
      'Missing zone list'
    );

    $zone->set('dm_area_id', $toArea->id)->save();

    $this->sortZones($zoneList);

    return $this->renderText('ok');
  }

  protected function sortZones(array $zoneList)
  {
    $zones = array();

    foreach($zoneList as $position => $zoneId)
    {
      $zones[$zoneId] = $position+1;
    }

    try
    {
      dmDb::table('DmZone')->doSort($zones);
    }
    catch(Exception $e)
    {
      if ($this->getUser()->can('system'))
      {
        throw $e;
      }

      $this->getUser()->logError(dm::getI18n()->__('A problem occured when sorting the items'));
    }

  }

}