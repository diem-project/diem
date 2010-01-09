<?php

class dmTestProjectBuilder
{
  protected
  $context;

  public function __construct(dmContext $context)
  {
    $this->context = $context;
  }

  public function execute()
  {
    $this->loremize(10);
    
    $this->loginForm();
  }

  protected function loremize($nb)
  {
    $task = new dmLoremizeTask($this->context->getEventDispatcher(), new sfFormatter());
    $task->run(array(), array('nb' => $nb));

    $this->context->get('page_tree_watcher')->synchronizePages();
    $this->context->get('page_tree_watcher')->synchronizeSeo();
  }

  protected function loginForm()
  {
    dmDb::table('DmWidget')->create(array(
      'module' => 'main',
      'action' => 'loginForm'
    ))
    ->set('Zone', dmDb::table('DmPage')->findOneByModuleAndAction('main', 'login')->PageView->Area->Zones[0])
    ->save();
  }

}