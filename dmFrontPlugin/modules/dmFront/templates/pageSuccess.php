<?php
/*
 * Render a page.
 * Layout areas and page content area are rendered.
 * 
 * Available vars :
 * - dmFrontPageHelper $helper      ( page_helper service )
 * - boolean           $isEditMode  ( whether the user is allowed to edit page )
 */
?>

<div id="dm_page"<?php $isEditMode && print ' class="edit"' ?>>

  <div class="dm_layout">

    <?php echo $helper->renderArea('layout.top', '.clearfix') ?>

    <div class="dm_layout_center clearfix">

      <?php echo $helper->renderArea('layout.left') ?>

      <?php echo $helper->renderArea('page.content') ?>

      <?php echo $helper->renderArea('layout.right') ?>

    </div>

    <?php echo $helper->renderArea('layout.bottom', '.clearfix') ?>

  </div>

</div>