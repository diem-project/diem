<?php
class dmFrontRecursivePageList extends dmRecursivePageList
{

  protected function getPageLink(array $page)
  {
    return '<a class="s16 s16_page_'.($page[1] === 'show' ? 'auto' : 'manual').'" href="'.$page[6].'">'.$page[5].'</a>';
  }

}