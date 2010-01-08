<?php

class dmFrontUserClipboard
{
  protected
  $user;

  public function __construct(dmFrontUser $user)
  {
    $this->user = $user;
  }

  public function getWidget()
  {
    return ($id = $this->load('widget_id')) ? dmDb::table('DmWidget')->findOneByIdWithI18n($this->load('widget_id')) : null;
  }

  public function setWidget(DmWidget $widget)
  {
    $this->save('widget_id', $widget->id);
  }

  public function getZone()
  {
    return ($id = $this->load('zone_id')) ? dmDb::table('DmZone')->findOneById($id) : null;
  }

  public function setZone(DmZone $zone)
  {
    $this->save('zone_id', $zone->id);
  }

  protected function load($name)
  {
    return $this->user->getAttribute($name, null, 'dm.front_user_clipboard');
  }

  protected function save($name, $value)
  {
    return $this->user->setAttribute($name, $value, 'dm.front_user_clipboard');
  }

}