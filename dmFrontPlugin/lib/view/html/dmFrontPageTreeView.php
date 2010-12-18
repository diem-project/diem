<?php

class dmFrontPageTreeView extends dmPageTreeView
{

  protected function renderPageLink(array $page)
  {
    if(sfConfig::get('dm_i18n_prefix_url')){
      $page[6] = $this->culture . (strlen($page[6])>0 ? '/' . $page[6] : '');
    }
     
    return '<a href="'.$page[6].'" data-page-id="'.$page[0].'"><ins></ins>'.$page[5].'</a>';
  }

  protected function getRecordTreeQuery()
  {
    return parent::getRecordTreeQuery()->addSelect('pageTranslation.slug');
  }

}