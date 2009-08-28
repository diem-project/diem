<?php

echo £o('div.dm_box.little');

  echo £('h1.title', 'Launch services');

  echo £o('div.dm_box_inner');

  echo £o('ul.services');

  foreach($services as $service)
  {
  	echo £('li',
      £link('dmService/launch?name='.$service)->text($service)->set('.service')->param('redirect', 0)
  	);
  }

  echo £c('ul');

  echo £c('div');

echo £c('div');