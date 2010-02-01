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
    return ($data = $this->load('widget')) ? dmDb::table('DmWidget')->findOneByIdWithI18n($data['id']) : null;
  }

  public function getMethod()
  {
    return ($data = $this->load('widget')) ? $data['method'] : null;
  }

  public function copy(DmWidget $widget)
  {
    $this->save('widget', array('method' => 'copy', 'id' => $widget->id));
  }

  public function cut(DmWidget $widget)
  {
    $this->save('widget', array('method' => 'cut', 'id' => $widget->id));
  }

  public function paste(DmZone $zone)
  {
    if(!$widget = $this->getWidget())
    {
      return;
    }

    if('cut' == $this->getMethod())
    {
      $widget->set('dm_zone_id', $zone->get('id'));

      // cutted then pasted widget becomes copied widget
      $this->save('widget', array('method' => 'copy', 'id' => $widget->id));
    }
    else
    {
      $widget->get('Translation');
      $widget = $widget->copy(true);
      $widget->set('dm_zone_id', $zone->get('id'));
    }

    $widget->save();
    
    return $widget;
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