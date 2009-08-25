<?php

$folderId = $sf_user->getAttribute('dm_media_browser_folder_id', 0);

echo £o('div#dm_media_bar.dm');

	echo £o('div#dm_media_bar_inner');

	  echo £('p.title', __('Media library'));

	  echo '<div id="dm_media_browser" class="{ folder_id: '.$folderId.' }">';

	  //include_partial('dmInterface/mediaBarInner', array('folder' => $folder));

	  echo '</div>';

  echo £c('div');

echo £c('div');

echo '<div id="dm_media_bar_toggler"></div>';