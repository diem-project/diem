<?php

class dmPageRoute
{
  protected
  $slug,
  $page,
  $culture;
  
  public function __construct($slug, DmPage $page, $culture)
  {
    $this->slug     = $slug;
    $this->page     = $page;
    $this->culture  = $culture;
  }
  
  public function getSlug()
  {
    return $this->slug;
  }
  
  public function getPage()
  {
    return $this->page;
  }
  
  public function getCulture()
  {
    return $this->culture;
  }
}