<?php

class dmFrontLinkTagRecord extends dmFrontLinkTagPage
{
  protected
  $record;

  protected function configure()
  {
    $this->record = $this->get('source');
    
    $timer = dmDebug::timerOrNull('DmFrontLinkTagRecord : fetch Record Page');
    
    $this->set('source', DmDb::table('DmPage')->findOneByRecordWithI18n($this->record));
    
    $timer && $timer->addTime();

    if (!$this->get('source') instanceof DmPage)
    {
      throw new dmException(sprintf('Can not link record %s %d because it has no page', get_class($this->record), $this->record->id));
    }
    
    parent::configure();
  }

}