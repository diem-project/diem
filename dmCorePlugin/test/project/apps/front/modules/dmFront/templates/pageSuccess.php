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
echo _open('div#dm_page'.($isEditMode ? '.edit' : ''));

echo $helper->renderAccessLinks();             // render accessibility links

  echo _tag('div.dm_layout',                      // open the layout div

    $helper->renderArea('layout.top', '.clearfix').   // render TOP Area

    _tag('div.dm_layout_center.clearfix',         // open the layout_center div

      $helper->renderArea('layout.left').             // render LEFT Area

      $helper->renderArea('page.content').          // render page content Area

      $helper->renderArea('layout.right')             // render right Area

    ).                                         // close the layout_center div

    $helper->renderArea('layout.bottom', '.clearfix') // render the BOTTOM Area

  );                                           // close the layout div

echo _close('div');                                // close the page's div wrapper