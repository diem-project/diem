<?php

class dmBrowserCheck
{
  protected
  $user;

  public function __construct(dmCoreUser $user)
  {
    $this->user = $user;
  }

  public function check()
  {
    if($this->user->getAttribute('browser_check', false, 'dm'))
    {
      return true;
    }

    $isSupportedBrowser = $this->isSupportedBrowser($this->user->getBrowser());

    $this->user->setAttribute('browser_check', $isSupportedBrowser, 'dm');

    return $isSupportedBrowser;
  }

  public function markAsChecked()
  {
    $this->user->setAttribute('browser_check', true, 'dm');
  }

  /*
   * Subjective list of supported browsers which can access to Diem admin
   */
  public function isSupportedBrowser(dmBrowser $browser)
  {
    switch($browser->getName())
    {
      case 'firefox':
        $isSupported = version_compare($browser->getVersion(), 3.5) >= 0; break;
      case 'safari':
        $isSupported = version_compare($browser->getVersion(), 4) >= 0; break;
      case 'chrome':
        $isSupported = version_compare($browser->getVersion(), 3) >= 0; break;
      case 'opera':
        $isSupported = version_compare($browser->getVersion(), '10.50') >= 0; break;
      case 'googlebot':
      	$isSupported = true;
      default:
        $isSupported = false;
    }

    return $isSupported;
  }
}
