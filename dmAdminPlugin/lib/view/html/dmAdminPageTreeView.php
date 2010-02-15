<?php

class dmAdminPageTreeView extends dmPageTreeView
{

  protected function getPageLink(array $page)
  {
    return '<a class="s16 s16_page_'.($page[1] === 'show' ? 'auto' : 'manual').'">'.$page[5].'</a>';
  }

}