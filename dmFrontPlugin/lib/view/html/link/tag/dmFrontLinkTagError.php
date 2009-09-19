<?php

class dmFrontLinkTagError extends dmFrontLinkTag
{
  protected
  $exception;
 
  protected function initialize()
  {
    parent::initialize();
    
    $this->exception = $this->resource->getSubject();
  }

  protected function getBaseHref()
  {
    return $this->requestContext['uri'];
  }

  public function render()
  {
    if (sfConfig::get('sf_debug'))
    {
      $this
      ->text('[EXCEPTION] '.$this->exception->getMessage())
      ->param('dm_debug', 1)
      ->title('Click me to see the exception details');
    }
    else
    {
      
    }
    
    return parent::render();
  }

}