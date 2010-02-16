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
        $isSupported = version_compare(3, $browser->getVersion()); break;
//      case 'safari':
//        $isSupported = version_compare(4, $browser->getVersion()); break;
      case 'chrome':
        $isSupported = version_compare(3, $browser->getVersion()); break;
      default:
         $isSupported = false;
    }

    return $isSupported;
  }
}
