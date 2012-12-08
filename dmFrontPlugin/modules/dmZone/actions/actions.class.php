<?php

class dmZoneActions extends dmFrontBaseActions
{

  public function executeEdit(sfWebRequest $request)
  {
    $this->forward404Unless(
      $this->zone = dmDb::table('DmZone')->find($request->getParameter('zone_id'))
    );

    $this->form = $this->getServiceContainer()
    ->setParameter('zone_form.object', $this->zone)
    ->getService('zone_form')
    ->removeCsrfProtection();

    if ($request->isMethod('post') && $this->form->bindAndValid($request))
    {
      $this->form->updateObject();
      
      if ($request->hasParameter('and_save'))
      {
        $this->form->getObject()->save();
        return $this->renderText('ok');
      }
    }
    
    return $this->renderAsync(array(
      'html'  => $this->getPartial('dmZone/edit'),
      'js'    => array('lib.hotkeys')
    ), true);
  }

  public function executeGetAttributes(sfWebRequest $request)
  {
    $bm = $this->getService('behaviors_manager');
    $user = $this->getService('user');
    $this->forward404Unless(
      $zone = dmDb::query('DmZone z')
      ->where('z.id = ?', $request->getParameter('zone_id'))
      ->select('z.width as width, z.css_class as css_class')
      ->limit(1)
      ->fetchPDO()
    );
    if ($bm->isZoneAttachable() && ($user->can('behavior_add') || $user->can('behavior_edit') || $user->can('behavior_delete') || $user->can('behavior_sort'))) {
        $zone[0][1] = $zone[0][1] . '.dm_behaviors_attachable';
    }
    
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

    return $this->renderText($this->getService('page_helper')->renderZone($zone->toArray(), true));
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