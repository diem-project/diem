<?php

$html = '';
foreach(array('info', 'notice', 'alert', 'error') as $log_type)
{
  foreach(array('dm_log_', '') as $prefix)
  {
    if (count($messages = (array)$sf_user->getFlash($prefix.$log_type)))
    {
      $class = $log_type === 'notice' ? 'info' : $log_type;
      $html .= _open("ul.flashs.".$class.'s');
      foreach($messages as $message)
      {
        $html .= _tag("li.flash.ui-corner-all.".$class,
          _tag('span.icon.fleft.mr5.s16block.s16_'.$class).
          nl2br(__($message, array(), 'admin'))
        );
      }
      $html .= _close("ul");
    }
  }
}

if ($html)
{
  echo _tag("div#flash", $html);
}