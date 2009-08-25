<?php

class dmAgent
{

	protected
	  $platform,
	  $browserName,
	  $browserVersion,
    $isMobileDevice,
	  $isRssReader,
	  $isCrawler,
	  $isBanned;

	public function __construct($browserName, $platform = null, $browserVersion = null, $isMobileDevice = false, $isRssReader = false, $isCrawler = false, $isBanned = false)
	{
    $this->browserName     = $browserName;
    $this->platform         = $platform == "unknown" ? null : $platform;
    if (!$browserVersion || trim($browserVersion) == "?")
    {
      $this->browserVersion = null;
    }
    else
    {
      $this->browserVersion = $browserVersion;
    }
    $this->isMobileDevice = (boolean) $isMobileDevice;
    $this->isRssReader    = (boolean) $isRssReader;
    $this->isCrawler       = (boolean) $isCrawler;
    $this->isBanned        = (boolean) $isBanned;
	}

  public function isHuman()
  {
  	return $this->isBrowser();
  }

  public function isBrowser()
  {
    return !$this->isRssReader && !$this->isCrawler && !$this->isUnknown();
  }

  public function isUnknown()
  {
  	return $this->browserName == "Unknown";
  }

  public function getPlatform() { return $this->platform; }
  public function getBrowserName() { return $this->browserName; }
  public function getBrowserVersion() { return $this->browserVersion; }
  public function getIsMobileDevice() { return $this->isMobileDevice; }
  public function getIsRssReader() { return $this->isRssReader; }
  public function getIsCrawler() { return $this->isCrawler; }
  public function getIsBanned() { return $this->isBanned; }


  public function setBrowserName($v) { $this->browserName = (string) $v; }
  public function setBrowserVersion($v) { $this->browserVersion = (string) $v; }
  public function setIsMobileDevice($v) { $this->isMobileDevice = (boolean) $v; }
  public function setIsRssReader($v) { $this->isRssReader = (boolean) $v; }
  public function setIsCrawler($v) { $this->isCrawler = (boolean) $v; }
  public function setIsBanned($v) { $this->isBanned = (boolean) $v; }

  protected static function createFromBrowscap(stdClass $b, $ua)
  {
  	if ($b->Browser == "Default Browser")
  	{
  	  $agent = self::createCustom($b, $ua);
  	}
    else
    {
  	  $agent = new dmAgent(
        $b->Browser,
    	  $b->Platform,
    	  $b->Version,
    	  $b->isMobileDevice,
    	  $b->isSyndicationReader,
    	  $b->Crawler,
    	  $b->isBanned
    	);
    }
    $agent = self::improve($agent, $ua);
    return $agent;
  }

  // améliore les informations de browscap
  protected static function improve(dmAgent $agent, $ua)
  {
    if(strpos($ua, "MJ12bot") !== false)
    {
      $agent->setIsBanned(true);
    }
    elseif($agent->getBrowserName() == "Feedfetcher-Google")
    {
      $agent->setIsBanned(false);
    }
    elseif(strpos($ua, "Shiretoko/") !== false)
    {
      $agent->setBrowserName("Firefox");
      $agent->setBrowserVersion(preg_replace("|^.*Shiretoko/(.+)$|i", '$1', $ua));
    }
    return $agent;
  }

  // quand browscap échoue à détecter l'agent, je tente aussi ma chance
  protected static function createCustom(stdClass $b, $ua)
  {
    if(strpos($ua, "powered by Diem") !== false)
    {
      $agent = new dmAgent("Diem", "Linux");
      $agent->setBrowserVersion(preg_replace("|^.*Diem/([0-9]+\.[0-9]+\.[0-9]+).*$|i", '$1', $ua));
      $agent->setIsCrawler(true);
    }
    elseif(strpos($ua, "powered by DMS") !== false)
    {
      $agent = new dmAgent("DMS", "Linux");
      $agent->setBrowserVersion(preg_replace("|^.*DMS/([0-9]+\.[0-9]+\.[0-9]+).*$|i", '$1', $ua));
      $agent->setIsCrawler(true);
    }
    elseif(strpos($ua, "Ruby/") !== false)
    {
      $agent = new dmAgent("Ruby");
      $agent->setIsRssReader(true);
    }
    elseif(strpos($ua, "RSSOwl/") !== false)
    {
      $agent = new dmAgent("RSSOwl");
      $agent->setIsRssReader(true);
    }
    elseif(strpos($ua, "Apple-PubSub/") !== false)
    {
      $agent = new dmAgent("Apple-PubSub");
      $agent->setIsRssReader(true);
    }
    elseif(strpos($ua, "FollowSite Bot") !== false)
    {
      $agent = new dmAgent("FollowSite Bot");
      $agent->setIsCrawler(true);
    }
    elseif(strpos($ua, "Vienna/") !== false)
    {
      $agent = new dmAgent("Vienna");
      $agent->setBrowserVersion(preg_replace("|^Vienna/([0-9]+\.[0-9]+\.[0-9]+).*$|i", '$1', $ua));
      $agent->setIsRssReader(true);
    }
    elseif(strpos($ua, "FriendFeedBot/") !== false)
    {
      $agent = new dmAgent("FriendFeedBot");
      $agent->setBrowserVersion(preg_replace("|^FriendFeedBot/([0-9]+\.[0-9]+).*$|i", '$1', $ua));
      $agent->setIsRssReader(true);
    }
    elseif(strpos($ua, "Twitturly/") !== false)
    {
      $agent = new dmAgent("Twitturly");
      $agent->setIsCrawler(true);
    }
    elseif(strpos($ua, "AideRSS/") !== false)
    {
      $agent = new dmAgent("AideRSS");
      $agent->setIsRssReader(true);
    }
    elseif(strpos($ua, "SPIP-") !== false)
    {
      $agent = new dmAgent("Spip");
      $agent->setBrowserVersion(preg_replace("|^SPIP-/([0-9]+\.[0-9]+\.[0-9]+).*$|i", '$1', $ua));
      $agent->setIsRssReader(true);
    }
    else
    {
      $agent = new dmAgent("Inconnu");
//      $agent->setIsCrawler(true);
//      $agent->setIsBanned(true);
    }
    return $agent;
  }

  public static function retrieveByUserAgent($ua)
  {
  	$uaMd5 = md5($ua);
  	if (!$agent = self::getAgentsCache()->get($uaMd5, array()))
  	{
  		try
  		{
        $agent = self::createFromBrowscap(self::getBrowscap($ua), $ua);
	      self::getAgentsCache()->set($uaMd5, $agent);
  	  }
  	  catch(Exception $e)
  	  {
  	  	dm::getUser()->logAlert('Browscap returned an error');
            /*
             * Browscap can not determine user agent.
             * Maybe because current server can no access Internet
             * so browscap list can not be downloaded
             */
            //throw new dmException("Invalid agent : $agent\n".$serverInfo['HTTP_USER_AGENT']);
            $agent = new dmAgent('Unknown browser', 'Unknown platform', '1.0');
  	  }
  	}
  	return $agent;
  }

  public static function getBrowscap($ua = null)
  {
  	$browscapDataPath = dmOs::join(sfConfig::get("sf_cache_dir"), "dm/browscap");

  	if(dmFilesystem::get()->mkdir($browscapDataPath))
  	{
      try
      {
	      if (!file_exists($browscapDataFile = dmOs::join($browscapDataPath, 'browscap.ini')))
	      {
	        if (!copy(dmOs::join(sfConfig::get("dm_core_dir"), "lib/vendor/browscap/browscap.ini"), $browscapDataFile))
	        {
	          throw new dmException(sprintf('Can not copy browcap data file to project cache'));
	        }
	      }
  	    $bc = new dmBrowscap($browscapDataPath, $browscapDataFile);
  	    $browser = $bc->getBrowser($ua);
      }
      catch(Exception $e)
      {
        dmDebug::log("Browscap Panic : ".$e->getMessage());
        throw $e;
      }
  	}
  	else
  	{
      dmDebug::log("Browscap Panic : $browscapDataPath is not writable");
      throw $e;
  	}

  	return $browser;
  }

  public static function getAgentsCache()
  {
  	return dmCacheManager::getCache("dm/user/agent");
  }

}