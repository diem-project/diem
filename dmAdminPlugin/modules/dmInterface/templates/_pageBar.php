<?php

echo _open('div#dm_page_bar');

  echo _tag('p.title', __('Site tree')._link('dmPage/tree')->text(__('Edit'))->set('.fright.mr10.s16.s16_sort'));

  echo '<div id="dm_page_tree" class="dm_tree"></div>';

echo _close('div');

echo '<div id="dm_page_bar_toggler"></div>';