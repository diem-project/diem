  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $notice = $form->getObject()->isNew() ? 'The item was created successfully.' : 'The item was updated successfully.';

      try {
        $<?php echo $this->getSingularName() ?> = $form->save();
      } catch (Doctrine_Validator_Exception $e) {

        $errorStack = $form->getObject()->getErrorStack();

        $message = get_class($form->getObject()) . ' has ' . count($errorStack) . " field" . (count($errorStack) > 1 ?  's' : null) . " with validation errors: ";
        foreach ($errorStack as $field => $errors) {
            $message .= "$field (" . implode(", ", $errors) . "), ";
        }
        $message = trim($message, ', ');

        $this->getUser()->setFlash('error', $message);
        return sfView::SUCCESS;
      }

      $this->dispatcher->notify(new sfEvent($this, 'admin.save_object', array('object' => $<?php echo $this->getSingularName() ?>)));
      
      if ($request->hasParameter('_save_and_add'))
      {
        $this->getUser()->setFlash('notice', $notice.' You can add another one below.');

        $redirection = $this->getRouteArrayForAction('new');
      }
      elseif ($request->hasParameter('_save_and_list'))
      {
        $this->getUser()->setFlash('notice', $notice);

        $redirection = $this->getRouteArrayForAction('index');
      }
      elseif ($request->hasParameter('_save_and_next'))
      {
        $this->getUser()->setFlash('notice', $notice);
        $redirection = $this->getRouteArrayForAction('edit', dmArray::get($<?php echo $this->getSingularName() ?>->getPrevNextRecords($this->buildQuery()), 'next', $this->form->getObject()));
      }
      else
      {
        $this->getUser()->setFlash('notice', $notice);

        $redirection = $this->getRouteArrayForAction('edit', $<?php echo $this->getSingularName() ?>);
      }

      $this->redirect($redirection);
    }
    else
    {
      $this->getUser()->setFlash('error', 'The item has not been saved due to some errors.', false);
    }
  }
