<?php

echo _open('div#dm_page_bar.dm');

  echo _tag('p.title', __('Site tree'));

  echo '<div id="dm_page_tree" class="dm_tree"></div>';

echo _close('div');

echo '<div id="dm_page_bar_toggler" class="dm"></div>';