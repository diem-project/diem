<style type="text/css">
div.viewport {
  width: 100%;
  height: 300px;
  cursor: move;
}
</style>
<?php

$jQueryMap = false;

foreach($dicImages as $appName => $image)
{
  if (!$image) continue;

  if($jQueryMap)
  {
    echo £('div.dm_box.big.diagram',
      £('div.title',
      £('h2', dmString::camelize($appName).' : Dependency Injection Container'.£link($image)->text('Download'))).
      £('div.dm_box_inner',
        ($withDispatcherLinks
        ? £('p.s16.s16_info', £link('+/dmDiagram/index?with_dispatcher_links=0')->text('Hide dispatcher dependencies'))
        : £('p.s16.s16_info', 'As nearly all modules have a reference to dispatcher, these dependencies are hidden. '.£link('+/dmDiagram/index?with_dispatcher_links=1')->text('Click here to see them'))
        ).
        £('div.viewport',
          £('div.toplevel height=300px').
          £('div', array('width' => £media($image)->getWidth(), 'height' => £media($image)->getHeight()),
            £media($image).
            £('div.mapcontent')
          )
        )
      )
    );
  }
  else
  {
    echo £media($image);
  }
}

if (!empty($mldProjectImage))
{
  echo £('div.dm_box.big.diagram', £('div.title', £('h2', 'Project Database')).£('div.dm_box_inner',
    £('div.full_width_image', £link($mldProjectImage)->text(£media($mldProjectImage)))
  ));
}

if (!empty($mldUserImage))
{
  echo £('div.dm_box.big.diagram', £('div.title', £('h2', 'Diem User Database')).£('div.dm_box_inner',
    £('div.full_width_image', £link($mldUserImage)->text(£media($mldUserImage)))
  ));
}

if (!empty($mldCoreImage))
{
  echo £('div.dm_box.big.diagram', £('div.title', £('h2', 'Diem Core Database')).£('div.dm_box_inner',
    £('div.full_width_image', £link($mldCoreImage)->text(£media($mldCoreImage)))
  ));
}