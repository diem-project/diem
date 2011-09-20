<?php

class dmAdminI18n extends dmI18n
{

  /**
   * Search first in dm catalogue, then in requested catalogue and finally in messages catalogue
   * 
   * @todo rethink the order of catalogues. dm catalogue is for dm strings.
   * 				given $catalogue is when developer writes code himself (within templates or by calling i18n service)
   * 				not sure messages as latest is good thing, as nearly everything will be within messages
   * 				as it is default in dmI18n->addTranslations();
   * 				good thing is that __() should be cache'd
   * 
   */
  public function __($string, $args = array(), $catalogue = 'dm', $forceCatalogue = false)
  {
    if(empty($catalogue))
    {
      $catalogue = 'dm';
    }
    
    $result = $this->__orFalse($string, $args, $forceCatalogue ? $catalogue : 'dm');

    if (false === $result && $catalogue !== 'dm')
    {
      $result = $this->__orFalse($string, $args, $catalogue);
    }
    
    if(false === $result)
    {
    	$result = $this->__orFalse($string, $args, 'messages');
    }
    
    if (false === $result)
    {
      $result = $this->handleNotFound($string, $args, $catalogue);
    }
    
    return $result;
  }

}