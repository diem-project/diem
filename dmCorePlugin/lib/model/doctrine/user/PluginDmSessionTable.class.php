<?php
/**
 */
class PluginDmSessionTable extends myDoctrineTable
{
  protected
  $current;

  public function findOneByIpAndUserAgent($ip, $ua)
  {
    return dmDb::query('DmSession s')
    ->where('s.ip = ? AND s.user_agent = ?', array($ip, $ua))
//    ->dmCache()
    ->fetchRecord();
  }

  public function findOneByBrowser($browserName, $browserVersion)
  {
    return dmDb::query('DmSession s')
    ->where('s.browser_name = ? AND s.browser_version = ?', array($browserName, $browserVersion))
//    ->dmCache()
    ->fetchRecord();
  }

  public function findOneBySessId($sessId)
  {
    return dmDb::query('DmSession s')
    ->where('s.sess_id = ?', $sessId)
//    ->dmCache()
    ->fetchRecord();
  }

  public function getCurrent($serverInfo)
  {
    if (is_null($this->current))
    {
      if (!$this->current = $this->findOneBySessId(session_id()))
      {
        if (!$this->current = $this->findOneByIpAndUserAgent($serverInfo['REMOTE_ADDR'], $serverInfo['HTTP_USER_AGENT']))
        {
          $agent = dmAgent::retrieveByUserAgent($serverInfo['HTTP_USER_AGENT']);
          if (!$agent->isHuman())
          {
            $this->current = $this->findOneByBrowser($agent->getBrowserName(), $agent->getBrowserVersion());
          }
          else
          {
            $this->current = $this->findOneByIpAndUserAgent($serverInfo['REMOTE_ADDR'], $serverInfo['HTTP_USER_AGENT']);

//            if(!$this->current && dm::getUser()->isAuthenticated())
//            {
//              $this->current = $this->findOneByDmProfileId(dm::getUser()->getProfileId());
//            }
          }
          // unknown session
          if (!$this->current)
          {
            $this->current = dmDb::create('DmSession')->create($serverInfo);
          }
        }
      }
    }

    return $this->current;
  }
}