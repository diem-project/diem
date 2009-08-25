<?php

class dmAdminRecursivePageList extends dmRecursivePageList
{

  protected function getPageLink(array $page)
  {
  	return sprintf('<a class="%s" href="%s">%s</a>',
  	  's16 s16_page_'.($page[1] === 'show' ? 'auto' : 'manual'),
  	  $page[6],
  	  $page[5]
  	);
  }

}