<?php

$folderId = $sf_user->getAttribute('dm_media_browser_folder_id', 0);

echo _open('div#dm_media_bar.dm');

  echo _open('div#dm_media_bar_inner');

    echo _tag('p.title', __('Media library'));

    echo '<div id="dm_media_browser" class="{ folder_id: '.$folderId.' }">';

    //include_partial('dmInterface/mediaBarInner', array('folder' => $folder));

    echo '</div>';

  echo _close('div');

echo _close('div');

echo '<div id="dm_media_bar_toggler" class="dm"></div>';