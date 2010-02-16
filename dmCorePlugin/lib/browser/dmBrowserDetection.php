<?php

class dmBrowserDetection
{
  protected
  $userAgent,
  $name,
  $version;

  public function execute($userAgent)
  {
    $this->userAgent = strtr(strtolower($userAgent), $this->getAliases());
    $this->name = $this->version = null;

    $this->guessFast();

    $this->fixGoogleChrome();

    $this->fixSafariVersion();

    $this->fixIphone();

    $this->fixYahoo();

    return array('name' => $this->name, 'version' => $this->version);
  }

  protected function guessFast()
  {
    // Clean up agent and build regex that matches phrases for known browsers
    // (e.g. "Firefox/2.0" or "MSIE 6.0" (This only matches the major and minor
    // version numbers.  E.g. "2.0.0.6" is parsed as simply "2.0"
    $pattern = '#('.join('|', $this->getKnownBrowsers()).')[/ ]+([0-9]+(?:\.[0-9]+)?)#';

    // Find all phrases (or return empty array if none found)
    if (preg_match_all($pattern, $this->userAgent, $matches))
    {
      // Since some UAs have more than one phrase (e.g Firefox has a Gecko phrase,
      // Opera 7,8 have a MSIE phrase), use the last one found (the right-most one
      // in the UA).  That's usually the most correct.
      $i = count($matches[1])-1;

      if (isset($matches[1][$i]))
      {
        $this->name = $matches[1][$i];
      }
      if (isset($matches[2][$i]))
      {
        $this->version = $matches[2][$i];
      }
    }
  }

  protected function fixGoogleChrome()
  {
    // Google chrome has a safari like signature
    if ('safari' === $this->name && strpos($this->userAgent, 'chrome/'))
    {
      $this->name = 'chrome';
      $this->version = preg_replace('|.+chrome/([0-9]+(?:\.[0-9]+)?).+|', '$1', $this->userAgent);
    }
  }

  protected function fixSafariVersion()
  {
    // Safari version is not encoded "normally"
    if ('safari' === $this->name && strpos($this->userAgent, ' version/'))
    {
      $this->version = preg_replace('|.+\sversion/([0-9]+(?:\.[0-9]+)?).+|', '$1', $this->userAgent);
    }
  }

  protected function fixIphone()
  {
    if('webkit' === $this->name && strpos($this->userAgent, '(iphone;'))
    {
      $this->name = 'iphone';
    }
  }

  protected function fixYahoo()
  {
    if (null === $this->name && strpos($this->userAgent, 'yahoo! slurp'))
    {
      $this->name = 'yahoobot';
    }
  }

  protected function getAliases()
  {
    return array(
      'shiretoko'     => 'firefox',
      'namoroka'      => 'firefox',
      'shredder'      => 'firefox',
      'minefield'     => 'firefox',
      'granparadiso'  => 'firefox'
    );
  }

  protected function getKnownBrowsers()
  {
    return array('msie', 'firefox', 'safari', 'webkit', 'opera', 'netscape', 'konqueror', 'gecko', 'chrome', 'googlebot', 'iphone', 'msnbot');
  }
}