<?php

echo £o('div.dm_box.little.mt20');

  echo £('h1.title', 'Launch services');

  echo £o('div.dm_box_inner');

  echo £o('ul.services');

  foreach($services as $service)
  {
  	echo £('li',
      £link('dmService/launch?name='.$service)->text($service)->set('.service')->param('redirect', false)
  	);
  }

  echo £c('ul');

  echo £c('div');

echo £c('div');