<?php

echo £o('div.dm_box.big.diagram');

echo £('div.title',
  £('h1', __('Diagrams'))
);

echo £o('div.dm_box_inner');

foreach($dicImages as $appName => $image)
{
  echo £('h2', dmString::camelize($appName).' : Dependency Injection Container');
  echo £media($image);
  
  echo '<hr />';
}

echo £('h2', 'Project Database');
echo £media($mldProjectImage);
  
  echo '<hr />';

echo £('h2', 'Diem User Database');
echo £media($mldUserImage);
  
  echo '<hr />';

echo £('h2', 'Diem Core Database');
echo £media($mldCoreImage);

echo £c('div');

echo £c('div');