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

<div id="dm_page" class="<?php $isEditMode && print 'edit' ?>">

  <?php echo $helper->renderAccessLinks() ?>

  <div class="layout">

    <?php echo $helper->renderArea('top', '.clearfix') ?>

    <div class="dm_layout_center clearfix">

      <?php echo $helper->renderArea('left') ?>

      <?php echo $helper->renderArea('content') ?>

      <?php echo $helper->renderArea('right') ?>

    </div>

    <?php echo $helper->renderArea('bottom', '.clearfix') ?>

  </div>

</div>