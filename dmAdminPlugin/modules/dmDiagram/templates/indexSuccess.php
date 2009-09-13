<style type="text/css">
.full_width_image {
  max-width: 100%;
  overflow-x: auto;
}
</style>
<?php

foreach($dicImages as $appName => $image)
{
  echo £('div.dm_box.big.diagram', £('div.title', £('h1', dmString::camelize($appName).' : Dependency Injection Container')).£('div.dm_box_inner',
    £link($image)->text(£media($image)->set('.full_width_image'))
  ));
}

echo £('div.dm_box.big.diagram', £('div.title', £('h1', 'Project Database')).£('div.dm_box_inner',
  £('div.full_width_image', £link($mldProjectImage)->text(£media($mldProjectImage)))
));

echo £('div.dm_box.big.diagram', £('div.title', £('h1', 'Diem User Database')).£('div.dm_box_inner',
  £('div.full_width_image', £link($mldUserImage)->text(£media($mldUserImage)))
));

echo £('div.dm_box.big.diagram', £('div.title', £('h1', 'Diem Core Database')).£('div.dm_box_inner',
  £('div.full_width_image', £link($mldCoreImage)->text(£media($mldCoreImage)))
));