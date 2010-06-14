<?php

class DmMediaTagFile extends DmMediaTag {

  public function render() {
    $tag = 
      '<img class="media" alt="' . dmOs::getFileName($this->getSrc()) . '" src="'
      .
      (file_exists(
        dmOs::join(
                sfConfig::get('sf_web_dir')
                . 
                  '/dmCorePlugin/images/media/'
                  .
                  dmOs::getFileExtension($this->getSrc(), false)
                  . '.png'
                )
              ) ? '/dmCorePlugin/images/media/'
                  .
                  dmOs::getFileExtension($this->getSrc(), false)
                  . '.png'
                 : '/dmCorePlugin/images/media/unknown.png'
              )
      .
      '" />';

    $tag = '<div class="file file_' . dmOs::getFileExtension($this->getSrc(), false) . '" ><div>' . dmOs::getFileName($this->getSrc()) . '</div></div>';

    return $tag;
  }

}