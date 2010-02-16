<?php

class dmAdminFullPageTreeView extends dmPageTreeView
{

  protected function getPageLink(array $page)
  {
    $type = $page[1] === 'show' ? 'auto' : 'manual';

    return '<a rel="'.$type.'">'.$page[5].'</a>';
  }
}