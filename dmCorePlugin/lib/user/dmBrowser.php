<?php

class dmBrowser
{
	protected
	$dispatcher,
	$name,
	$version;
	
	protected static
	$browserAliases = array(
    'shiretoko' => 'firefox',
    'namoroka'  => 'firefox'
	),
	$knownBrowsers = array('msie', 'firefox', 'safari', 'webkit', 'opera', 'netscape', 'konqueror', 'gecko', 'chrome');
	
	public function __construct(sfEventDispatcher $dispatcher)
	{
		$this->dispatcher = $dispatcher;
	}
	
  /*
   * Minimal browser detection from user agent.
   * It has the advantage of being compact and
   * fairly performant as well, since it doesn't
   * do any iteration or recursion.
   */
	public function configureFromUserAgent($userAgent)
	{
	  $formattedUserAgent = strtr(strtolower($userAgent), self::$browserAliases);
    
    // Clean up agent and build regex that matches phrases for known browsers
    // (e.g. "Firefox/2.0" or "MSIE 6.0" (This only matches the major and minor
    // version numbers.  E.g. "2.0.0.6" is parsed as simply "2.0"
    $pattern = '#(?<browser>'.join('|', self::$knownBrowsers).')[/ ]+(?<version>[0-9]+(?:\.[0-9]+)?)#';
  
    // Find all phrases (or return empty array if none found)
    if (preg_match_all($pattern, $formattedUserAgent, $matches)) 
    {
      // Since some UAs have more than one phrase (e.g Firefox has a Gecko phrase,
      // Opera 7,8 have a MSIE phrase), use the last one found (the right-most one
      // in the UA).  That's usually the most correct.
      $i = count($matches['browser'])-1;
      
      if (isset($matches['browser'][$i]))
      {
        $this->setName($matches['browser'][$i]);
      }
      if (isset($matches['version'][$i]))
      {
        $this->setVersion($matches['version'][$i]);
      }
    }
    else
    {
      $this->dispatcher->notify(new sfEvent($this, 'dm.browser.unknown', $userAgent));
    }
	}
	
	public function isUnknown()
	{
		return null === $this->name;
	}
	
	public function getName()
	{
		return $this->name;
	}
	
	public function setName($name)
	{
	  $this->name = $name;
	}
	
	public function getVersion()
	{
		return $this->version;
	}
  
  public function setVersion($version)
  {
    $this->version = $version;
  }
	
	/*
	 * Subjective modern browsers wich can access to Diem admin
	 */
	public function isModern()
	{
		switch($this->name)
		{
			case 'firefox':
				$isModern = version_compare(3, $this->version); break;
			case 'opera':
				$isModern = version_compare(9, $this->version); break;
			case 'safari':
				$isModern = version_compare(4, $this->version); break;
			case 'chrome':
				$isModern = version_compare(3, $this->version); break;
			default:
				 $isModern = false;
		}
		
		return $isModern;
	}
}