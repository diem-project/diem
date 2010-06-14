<?php

class DmMediaTagFile extends DmMediaTag {

  public function render() {
    $tag = '<div class="file file_' . dmOs::getFileExtension($this->getSrc(), false) . '" >';
    $tag .= '<a class="name" href="' . $this->getSrc() . '">' . dmOs::getFileName($this->getSrc()) . '</a></div>';

    return $tag;
  }

}