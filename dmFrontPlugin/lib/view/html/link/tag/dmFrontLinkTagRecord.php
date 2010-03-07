<?php

class dmFrontLinkTagRecord extends dmFrontLinkTagPage
{
  protected
  $record;

  protected function initialize(array $options = array())
  {
    $this->record = $this->resource->getSubject();
    
    $this->resource->setSubject($this->getRecordPage($this->record));

    if (!$this->resource->getSubject() instanceof DmPage)
    {
      throw new dmException(sprintf('Can not link record %s %d because it has no page', get_class($this->record), $this->record->id));
    }
    
    parent::initialize($options);
  }

  protected function getRecordPage(dmDoctrineRecord $record)
  {
    $page = dmDb::table('DmPage')->findOneByRecordWithI18n($record);

    if($page)
    {
      return $page;
    }

    // The record has no page yet, let's try to create it right now
    sfContext::getInstance()->get('page_tree_watcher')
    ->addModifiedTable($record->getTable())
    ->update();

    return dmDb::table('DmPage')->findOneByRecordWithI18n($record);
  }

}