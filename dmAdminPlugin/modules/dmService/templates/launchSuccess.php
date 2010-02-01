<?php

echo _tag('h1', $service);

echo _tag('p.mt20', sprintf('Terminated in %01.2f seconds.', $time));

echo _tag('p.mt20', sprintf('Average time : %01.2f seconds.', $time/$iterations));


echo _open('div.dm_box.little');

  echo _tag('h1.title', 'Launch services');

  echo _open('div.dm_box_inner');

  echo _open('ul.services');

  foreach($services as $service)
  {
    echo _tag('li',
      _link('dmService/launch?name='.$service)->text($service)->set('.service')->param('redirect', 0)
    );
  }

  echo _close('ul');

  echo _close('div');

echo _close('div');