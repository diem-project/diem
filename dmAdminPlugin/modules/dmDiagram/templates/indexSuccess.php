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
    echo _tag('div.dm_box.big.diagram',
      _tag('div.title',
      _tag('h2', dmString::camelize($appName).' : Dependency Injection Container'._link($image)->text('Download'))).
      _tag('div.dm_box_inner',
        ($withDispatcherLinks
        ? _tag('p.s16.s16_info', _link('+/dmDiagram/index?with_dispatcher_links=0')->text('Hide dispatcher dependencies'))
        : _tag('p.s16.s16_info', 'As nearly all modules have a reference to dispatcher, these dependencies are hidden. '._link('+/dmDiagram/index?with_dispatcher_links=1')->text('Click here to see them'))
        ).
        _tag('div.viewport',
          _tag('div.toplevel height=300px').
          _tag('div', array('width' => _media($image)->getWidth(), 'height' => _media($image)->getHeight()),
            _media($image).
            _tag('div.mapcontent')
          )
        )
      )
    );
  }
  else
  {
    echo _media($image);
  }
}

if (!empty($mldProjectImage))
{
  echo _tag('div.dm_box.big.diagram', _tag('div.title', _tag('h2', 'Project Database'))._tag('div.dm_box_inner',
    _tag('div.full_width_image', _link($mldProjectImage)->text(_media($mldProjectImage)))
  ));
}

if (!empty($mldUserImage))
{
  echo _tag('div.dm_box.big.diagram', _tag('div.title', _tag('h2', 'Diem User Database'))._tag('div.dm_box_inner',
    _tag('div.full_width_image', _link($mldUserImage)->text(_media($mldUserImage)))
  ));
}

if (!empty($mldCoreImage))
{
  echo _tag('div.dm_box.big.diagram', _tag('div.title', _tag('h2', 'Diem Core Database'))._tag('div.dm_box_inner',
    _tag('div.full_width_image', _link($mldCoreImage)->text(_media($mldCoreImage)))
  ));
}