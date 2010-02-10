<?php
/**
 */
class PluginDmLockTable extends myDoctrineTable
{

  /**
   * @return array usernames of current active users on the same page
   */
  public function getLocks(array $data)
  {
    $locks = dmDb::pdo(
      sprintf(
        'SELECT DISTINCT a.user_name FROM %s a WHERE a.user_id != ? AND a.module = ? AND a.action = ? AND a.record_id = ? AND a.time > ? ORDER BY a.user_name ASC',
        $this->getTableName()
      ),
      array($data['user_id'], $data['module'], $data['action'], $data['record_id'], $_SERVER['REQUEST_TIME'] - sfConfig::get('dm_locks_timeout', 10))
    )->fetchAll(PDO::FETCH_NUM);

    return $this->toUsernames($locks);
  }

  /**
   * @return array usernames of current active users
   */
  public function getUserNames()
  {
    $locks = dmDb::pdo(
      sprintf('SELECT DISTINCT a.user_name FROM %s a WHERE a.time > ? ORDER BY a.user_name ASC', $this->getTableName()),
      array($_SERVER['REQUEST_TIME'] - sfConfig::get('dm_locks_timeout', 10))
    )->fetchAll(PDO::FETCH_NUM);

    return $this->toUsernames($locks);
  }

  protected function toUsernames(array $locks)
  {
    $usernames = array();
    foreach($locks as $lock)
    {
      $usernames[] = $lock[0];
    }

    return $usernames;
  }

  /**
   * When a user displays a page
   */
  public function update(array $data)
  {
    $lock = $this->findOneByData($data);

    if(!$lock)
    {
      $lock = $this->create($data)->saveGet();
    }
    else
    {
      $lock->merge($data)->save();
    }

    $this->removeOldLocks();
  }

  /**
   * When receiving an ajax ping
   */
  public function ping(array $data)
  {
    $lock = $this->findOneByData($data);

    if($lock = $this->findOneByData($data))
    {
      $lock->merge($data)->save();
    }

    $this->removeOldLocks();
  }

  public function removeOldLocks()
  {
    dmDb::pdo(
      sprintf('DELETE FROM %s WHERE time < ?', $this->getTableName()),
      array($_SERVER['REQUEST_TIME'] - 10*sfConfig::get('dm_locks_timeout', 10))
    );
  }

  public function findOneByData(array $data)
  {
    return $this->createQuery('a')
    ->where('a.user_id = ?', $data['user_id'])
    ->andWhere('a.module = ?', $data['module'])
    ->andWhere('a.action = ?', $data['action'])
    ->andWhere('a.record_id = ?', $data['record_id'])
    ->fetchRecord();
  }
}