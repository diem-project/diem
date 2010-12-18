  public function executeDelete(sfWebRequest $request)
  {
    $record = $this->getObjectOrForward404($request);
  
    $this->dispatcher->notify(new sfEvent($this, 'admin.delete_object', array('object' => $record)));
    
    $record->delete();

    $this->getUser()->setFlash('notice', 'The item was deleted successfully.');

    $this->redirect('@<?php echo $this->getUrlForAction('list') ?>');
  }
