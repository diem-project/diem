<?php

class dmMediaTagAudio extends dmMediaTag
{

  public function render()
  {
    $tag = '<div'.$this->getHtmlAttributes().'></div>';

    return $tag;
  }
}