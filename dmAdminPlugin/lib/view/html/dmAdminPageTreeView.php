<?php

class dmAdminPageTreeView extends dmPageTreeView
{

  protected function renderPageLink(array $page)
  {
    return '<a data-page-id="'.$page[0].'"><ins></ins>'.$page[5].'</a>';
  }

}