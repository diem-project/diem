<?php

class dmFrontWebController extends sfFrontWebController
{
  /**
   * @see sfFrontWebController
   */
  public function redirect($url, $delay = 0, $statusCode = 302)
  {
    $this->dispatcher->notify(new sfEvent($this, 'dm.controller.redirect'));
    
    return parent::redirect($url, $delay, $statusCode);
  }
}