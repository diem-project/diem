<?php
/*
 * Renders a page.
 * Layout areas and page content area are rendered.
 * 
 * Available vars :
 * - dmFrontPageHelper $helper      ( page_helper service )
 * - boolean           $isEditMode  ( whether the user is allowed to edit page )
 */

// open the page's div wrapper
echo £o('div#dm_page'.($isEditMode ? '.edit' : ''));

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

echo £c('div');                                // close the page's div wrapper