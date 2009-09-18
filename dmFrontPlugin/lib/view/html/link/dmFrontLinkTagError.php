<?php

class dmFrontLinkTagError extends dmFrontLinkTag
{
  protected
  $exception;
 
  protected function configure()
  {
    $this->exception = $this->get('source');
  }

  protected function getBaseHref()
  {
    return dm::getRequest()->getUri();
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