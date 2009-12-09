<?php

/*
 * Used to test the thread launcher
 */
class dmThreadTest extends dmThread
{
  public function doExecute()
  {
    touch(dmOs::join(sfConfig::get('sf_cache_dir'), $this->options['proof_file_name']));
  }
}