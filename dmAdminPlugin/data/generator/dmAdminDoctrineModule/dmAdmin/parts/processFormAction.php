  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $notice = $form->getObject()->isNew() ? 'The item was created successfully.' : 'The item was updated successfully.';

      $<?php echo $this->getSingularName() ?> = $form->save();

      $this->dispatcher->notify(new sfEvent($this, 'admin.save_object', array('object' => $<?php echo $this->getSingularName() ?>)));

      if ($request->hasParameter('_save_and_add'))
      {
        $this->getUser()->setFlash('notice', $notice.' You can add another one below.');

        $redirection = '@<?php echo $this->getUrlForAction('new') ?>';
      }
      elseif ($request->hasParameter('_save_and_list'))
      {
        $this->getUser()->setFlash('notice', $notice);

        $redirection = '@<?php echo $this->getUrlForAction('list') ?>';
      }
      elseif ($request->hasParameter('_save_and_next'))
      {
        $this->getUser()->setFlash('notice', $notice);
        $<?php echo $this->getSingularName() ?> = dmArray::get($<?php echo $this->getSingularName() ?>->getPrevNextRecords($this->buildQuery()), 'next', $this->form->getObject());
        $redirection = '@<?php echo $this->getUrlForAction('edit') ?>?<?php echo $this->getPrimaryKeyUrlParams() ?>;
      }
      else
      {
        $this->getUser()->setFlash('notice', $notice);

        $redirection = array('sf_route' => '<?php echo $this->getUrlForAction('edit') ?>', 'sf_subject' => $<?php echo $this->getSingularName() ?>);
      }

      $this->redirect($redirection);
    }
    else
    {
      $this->getUser()->setFlash('error', 'The item has not been saved due to some errors.', false);
    }
  }
