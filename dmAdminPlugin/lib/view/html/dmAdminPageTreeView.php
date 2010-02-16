<?php

class dmAdminPageTreeView extends dmPageTreeView
{

  protected function renderPageLink(array $page)
  {
    return '<a><ins></ins>'.$page[5].'</a>';
  }

}