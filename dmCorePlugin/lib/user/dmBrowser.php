<?php

class dmBrowser
{
	protected
	$name,
	$version;
	
	protected static
	$browserAliases = array(
    'shiretoko' => 'firefox',
    'namoroka'  => 'firefox'
	),
	$knownBrowsers = array('msie', 'firefox', 'safari', 'webkit', 'opera', 'netscape', 'konqueror', 'gecko', 'chrome');
	
	public function __construct($name, $version)
	{
		$this->name = $name;
		$this->version = $version;
	}
  
  /*
   * Minimal browser detection from user agent.
   * It has the advantage of being compact and
   * fairly performant as well, since it doesn't
   * do any iteration or recursion.
   * 
   * @return dmBrowser object
   */
  public static function buildFromUserAgent($userAgent)
  {
  	$userAgent = strtr(strtolower($userAgent), self::$browserAliases);
  	
    // Clean up agent and build regex that matches phrases for known browsers
    // (e.g. "Firefox/2.0" or "MSIE 6.0" (This only matches the major and minor
    // version numbers.  E.g. "2.0.0.6" is parsed as simply "2.0"
    $pattern = '#(?<browser>'.join('|', self::$knownBrowsers).')[/ ]+(?<version>[0-9]+(?:\.[0-9]+)?)#';
  
    // Find all phrases (or return empty array if none found)
    if (!preg_match_all($pattern, $userAgent, $matches)) 
    {
      return new self(null, null);
    }
  
    // Since some UAs have more than one phrase (e.g Firefox has a Gecko phrase,
    // Opera 7,8 have a MSIE phrase), use the last one found (the right-most one
    // in the UA).  That's usually the most correct.
    $i = count($matches['browser'])-1;
    
    return new self($matches['browser'][$i], $matches['version'][$i]);
  }
	
	public function isUnknown()
	{
		return is_null($this->name);
	}
	
	public function getName()
	{
		return $this->name;
	}
	
	public function getVersion()
	{
		return $this->version;
	}
}