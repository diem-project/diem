<?php

class dmEventLogEntry extends dmLogEntry
{
  protected static
  $recordsCache   = array();
  
  public function configure(array $data)
  {
    $userId = dmArray::get($data, 'user_id', $this->serviceContainer->getService('user')->getUserId());
    
    if (!$userId && dmConfig::isCli())
    {
      $userId = 'task';
    }
    
    $this->data = array(
      'time'          => (string) $data['server']['REQUEST_TIME'],
      'ip'            => (string) $this->getCurrentRequestIp(),
      'session_id'    => (string) session_id(),
      'user_id'       => (string) $userId,
      'action'        => (string) $data['action'],
      'type'          => (string) $data['type'],
      'subject'       => dmString::truncate($data['subject'], 500),
      'record'        => isset($data['record']) ? get_class($data['record']).':'.$data['record']->get('id') : ''
    );
  }
  
  public function isError()
  {
    return $this->data['type'] == 'exception';
  }
  
  protected function getUser()
  {
    $userId = $this->get('user_id');
    
    if($userId && is_numeric($userId))
    {
      return $this->fetchRecord('DmUser', $userId);
    }
  }

  protected function getRecordObject()
  {
    if($record = $this->get('record'))
    {
      list($model, $id) = explode(':', $record);

      return $this->fetchRecord($model, $id);
    }
  }

  protected function fetchRecord($model, $id)
  {
    $key = $model.':'.$id;
    
    if (!isset(self::$recordsCache[$key]))
    {
      self::$recordsCache[$key] = dmDb::table($model)
      ->createQuery('r')
      ->where('r.id = ?', $id)
      ->fetchRecord();
    }

    return self::$recordsCache[$key];
  }

  protected function getUsername()
  {
    return ($user = $this->getUser())
    ? $user->get('username')
    : ('task' === $this->get('user_id')
      ? 'task'
      : '');
  }

}