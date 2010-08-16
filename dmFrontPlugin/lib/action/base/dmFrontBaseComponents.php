<?php

class dmFrontBaseComponents extends dmBaseComponents
{
  /**
   * @return DmPage the current page
   */
  public function getPage()
  {
    return $this->context->getPage();
  }
  
  /**
   * Preload all pages related to records
   */
  protected function preloadPages($records)
  {
    dmDb::table('DmPage')->preloadPagesForRecords($records);
  }
}
