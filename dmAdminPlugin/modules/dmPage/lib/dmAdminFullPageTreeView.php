<?php

class dmAdminFullPageTreeView extends dmPageTreeView
{

  protected function getPageLink(array $page)
  {
    $type = $page[1] === 'show' ? 'auto' : 'manual';

    return '<a class="s16 s16_page_'.$type.'" rel="'.$type.'">'.$page[5].'</a>';
  }
}