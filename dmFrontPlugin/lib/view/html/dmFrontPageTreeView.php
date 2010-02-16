<?php

class dmFrontPageTreeView extends dmPageTreeView
{

  protected function renderPageLink(array $page)
  {
    return '<a href="'.$page[6].'"><ins></ins>'.$page[5].'</a>';
  }

  protected function getRecordTreeQuery()
  {
    return parent::getRecordTreeQuery()->addSelect('pageTranslation.slug');
  }

}