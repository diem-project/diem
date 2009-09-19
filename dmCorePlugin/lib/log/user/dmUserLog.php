<?php

class dmUserLog extends dmFileLog
{
  protected
  $defaults = array(
    'file'                => 'data/dm/log/user.log',
    'entry_service_name'  => 'user_log_entry'
  );
}