<?php

// open the page div
echo £o('div#dm_page'.($sf_user->getIsEditMode() ? '.edit' : ''));

echo $helper->renderAccessLinks();             // render accessibility links

  echo £('div.dm_layout',                      // open the layout div

    $helper->renderArea('top', '.clearfix').   // render TOP Area

    £('div.dm_layout_center.clearfix',         // open the layout_center div

      $helper->renderArea('left').             // render LEFT Area

      $helper->renderArea('content').          // render page content Area

      $helper->renderArea('right')             // render right Area

    ).                                         // close the layout_center div

    $helper->renderArea('bottom', '.clearfix') // render the BOTTOM Area

  );                                           // close the layout div

echo £c('div');                                // close the page div