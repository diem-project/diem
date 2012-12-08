<?php

if (!$object || !$object->id)
{
  return;
}

use_helper('Date', 'DmMedia');

echo _open('div.dm_media_file');

echo _tag('h3.title.none', $object->getFile());

echo _open('div.clearfix');

  if ($object->isImage()) {
      $sf_response->addJavascript('lib.fancybox');
      $sf_response->addStylesheet('lib.fancybox');
      $sf_response->addJavascript('admin.fancyboxLaunch');
      
      echo _tag('div.view',
        _link($object->getFullWebPath())->text(_media($object)->size(250, 150))->set('.fancybox')->target('_blank')
      );
      
  } else {
      echo _tag('div.view',
        _link($object->getFullWebPath())->text(_media('dmCore/images/media/unknown.png')->size(64, 64))->target('_blank')
      );
  }

  echo _tag('div.content',

    _tag('div.infos',
      definition_list(media_file_infos($object), '.clearfix.dm_little_dl')
    )
  );

echo _close('div');

echo _close('div');