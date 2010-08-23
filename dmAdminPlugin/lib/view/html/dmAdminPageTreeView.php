<?php

class dmAdminPageTreeView extends dmPageTreeView
{
  protected $moduleManager;

  public function __construct(dmModuleManager $moduleManager, dmHelper $helper, $culture, array $options)
  {
    $this->moduleManager = $moduleManager;
    parent::__construct($helper, $culture, $options);
  }

  protected function renderPageLink(array $page)
  {
    return '<a data-page-id="'.$page[0].'"><ins></ins>'.$page[6].'</a>';
  }

}
