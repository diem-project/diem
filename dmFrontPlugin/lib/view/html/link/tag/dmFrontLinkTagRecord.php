<?php

class dmFrontLinkTagRecord extends dmFrontLinkTagPage
{
  protected
  $record;

  protected function initialize()
  {
    $this->record = $this->resource->getSubject();
    
    $timer = dmDebug::timerOrNull('DmFrontLinkTagRecord : fetch Record Page');
    
    $this->resource->setSubject(dmDb::table('DmPage')->findOneByRecordWithI18n($this->record));
    
    $timer && $timer->addTime();

    if (!$this->resource->getSubject() instanceof DmPage)
    {
      throw new dmException(sprintf('Can not link record %s %d because it has no page', get_class($this->record), $this->record->id));
    }
    
    parent::initialize();
  }

}