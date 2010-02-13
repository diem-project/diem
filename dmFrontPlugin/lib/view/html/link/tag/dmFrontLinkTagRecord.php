<?php

class dmFrontLinkTagRecord extends dmFrontLinkTagPage
{
  protected
  $record;

  protected function initialize(array $options = array())
  {
    $this->record = $this->resource->getSubject();
    
    $this->resource->setSubject(dmDb::table('DmPage')->findOneByRecordWithI18n($this->record));

    if (!$this->resource->getSubject() instanceof DmPage)
    {
      throw new dmException(sprintf('Can not link record %s %d because it has no page', get_class($this->record), $this->record->id));
    }
    
    parent::initialize($options);
  }

}