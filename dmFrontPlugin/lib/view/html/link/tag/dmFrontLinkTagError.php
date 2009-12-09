<?php

class dmFrontLinkTagError extends dmFrontLinkTag
{
  protected
  $exception;
 
  protected function initialize(array $options = array())
  {
    parent::initialize($options);
    
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
      return $this
      ->text('[EXCEPTION] '.$this->exception->getMessage())
      ->param('dm_debug', 1)
      ->title('Click me to see the exception details')
      ->render();
    }
    else
    {
      return '';
    }
  }
  
  /*
   * The template writter may expect a dmFrontLinkTagPage instance
   * so we simulate it
   */
  public function __call($method, $arguments)
  {
    return $this;
  }

}