<style type="text/css">
.full_width_image {
  max-width: 100%;
  overflow-x: auto;
}
</style>
<?php

echo £('h1.title', 'Auto-generated diagrams');

foreach($dicImages as $appName => $image)
{
  echo £('div.dm_box.big.diagram', £('div.title', £('h2', dmString::camelize($appName).' : Dependency Injection Container')).£('div.dm_box_inner',
    £('div.full_width_image', £link($image)->text(£media($image)))
  ));
}

echo £('div.dm_box.big.diagram', £('div.title', £('h2', 'Project Database')).£('div.dm_box_inner',
  £('div.full_width_image', £link($mldProjectImage)->text(£media($mldProjectImage)))
));

echo £('div.dm_box.big.diagram', £('div.title', £('h2', 'Diem User Database')).£('div.dm_box_inner',
  £('div.full_width_image', £link($mldUserImage)->text(£media($mldUserImage)))
));

echo £('div.dm_box.big.diagram', £('div.title', £('h2', 'Diem Core Database')).£('div.dm_box_inner',
  £('div.full_width_image', £link($mldCoreImage)->text(£media($mldCoreImage)))
));