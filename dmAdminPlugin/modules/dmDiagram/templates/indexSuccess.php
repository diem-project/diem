<style type="text/css">
div.full_width_image {
  width: 100%;
  height: 450px;
}
div.full_width_image img.panview {
  cursor: move;
}
</style>
<?php

if (!empty($mldProjectImage))
{
  echo _tag('div.dm_box.big.diagram',
    _tag('div.title',
      _tag('h2', __('Project Database')._link($mldProjectImage)->text(__('Download'))->target('blank'))
    ).
    _tag('div.dm_box_inner',
      _tag('div.full_width_image', _media($mldProjectImage)->set('.panview#mld_project'))
    )
  );
}

if (!empty($mldUserImage))
{
  echo _tag('div.dm_box.big.diagram',
    _tag('div.title',
      _tag('h2', __('Diem User Database')._link($mldUserImage)->text(__('Download'))->target('blank'))
    ).
    _tag('div.dm_box_inner',
      _tag('div.full_width_image', _media($mldUserImage)->set('.panview#mld_user'))
    )
  );
}

if (!empty($mldCoreImage))
{
  echo _tag('div.dm_box.big.diagram',
    _tag('div.title',
      _tag('h2', __('Diem Core Database')._link($mldCoreImage)->text(__('Download'))->target('blank'))
    ).
    _tag('div.dm_box_inner',
      _tag('div.full_width_image', _media($mldCoreImage)->set('.panview#mld_core'))
    )
  );
}

foreach($dicImages as $appName => $image)
{
  if (!$image) continue;

  echo _tag('div.dm_box.big.diagram',
    _tag('div.title',
    _tag('h2', dmString::camelize($appName).__(' : Dependency Injection Container')._link($image)->text(__('Download'))->target('blank'))).
    _tag('div.dm_box_inner',
      ($withDispatcherLinks
      ? _tag('p.s16.s16_info', _link('+/dmDiagram/index?with_dispatcher_links=0')->text(__('Hide dispatcher dependencies')))
      : _tag('p.s16.s16_info', __('As nearly all modules have a reference to dispatcher, these dependencies are hidden. ')._link('+/dmDiagram/index?with_dispatcher_links=1')->text(__('Click here to see them')))
      ).
      _tag('div.full_width_image', _media($image)->set('.panview#panview'.$appName))
    )
  );
}