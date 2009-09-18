<?php

echo £o('div#dm_page'.($sf_user->getIsEditMode() ? '.edit' : ''));

echo $helper->renderAccessLinks();

  echo £('div.dm_layout',

    $helper->renderArea('top').

    £('div.dm_layout_center.clearfix',

      $helper->renderArea('left').

      $helper->renderArea('content').

      $helper->renderArea('right')

    ).

    $helper->renderArea('bottom')

  );

echo £c('div');