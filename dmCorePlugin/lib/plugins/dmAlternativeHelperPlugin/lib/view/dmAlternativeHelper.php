<?php

class dmAlternativeHelper extends dmHelper
{
  /*
   * a, class='tagada ergrg' id=zegf, contenu
   * a class=tagada id=truc, contenu
   * a, contenu
   * a, array(), contenu
   * a#truc.tagada, contenu
   */
  public function _tagO($tagName, array $opt = array())
  {
    return $this->£o($tagName, $opt);
  }

  public function _tagC($tagName)
  {
    return $this->£c($tagName);
  }

  public function _tag($tagName, $opt = array(), $content = false, $openAndClose = true)
  {
    return $this->£($tagName, $opt, $content, $openAndClose);
  }
  
  public function _link($source = null)
  {
    return $this->£link($source);
  }
  
  public function _media($source)
  {
    return $this->£media($source);
  }
  
  public function _table()
  {
    return $this->£table();
  }
}