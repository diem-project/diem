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

  <?php echo $helper->renderArea('default.top', '.clearfix') ?>

  <div class="centered_page_content clearfix">

    <?php echo $helper->renderArea('default.left') ?>

    <?php echo $helper->renderArea($page->module.'.'.$page->action.'.content', '.page_content') ?>

    <?php echo $helper->renderArea('default.right') ?>

  </div>

  <?php echo $helper->renderArea('default.bottom', '.clearfix') ?>

</div>