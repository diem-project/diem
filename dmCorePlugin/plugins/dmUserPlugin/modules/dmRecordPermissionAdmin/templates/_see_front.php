<?php if($form->getObject()->getDmModule() && $form->getObject()->getDmModule()->getOption('has_front')): ?>
<?php echo _open('a', array('href'=>$sf_context->getServiceContainer()->getService('controller')->genUrl(array(
          'sf_route' => $form->getObject()->getRecord()->getDmModule()->getUnderscore(),
          'action'   => 'show',
          'pk'       => $form->getObject()->getPrimaryKey())))) ?>
	<?php echo __('See in Admin');?>
<?php echo _close('a');?>
<?php endif;?>